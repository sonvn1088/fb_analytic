<?php

namespace App\Http\Controllers\Admin;


use App\Helps\Facebook;
use App\Models\Account;
use App\Models\Group;
use App\Models\MyPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$accounts = Account::all();
        return view('admin.accounts.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {

        $data = Datatables::of(Account::query())
            ->editColumn('status', function ($account){
                return $account->status;
            })
            ->editColumn('on_server', function ($account){
                return $account->on_server;
            })
            ->editColumn('role', function ($account){
                return $account->role;
            })
            ->editColumn('group_id', function ($account){
                return $account->group?$account->group->name:'';
            })
            ->make(true);

        return $data;
    }



    public function openProfile($id){
        exec('"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --profile-directory="Profile '.$id.'" https://www.facebook.com');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Account $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Account::find($id);
        if($account){
            $groups = Group::all();
            $groupsData = ['' => '--'];
            foreach($groups as $group){
                $groupsData[$group->id] = $group->name;
            }
            return view('admin.accounts.show', ['account' => $account, 'groups' => $groupsData]);
        }

        else
            return redirect()->intended(route('admin.accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Account $user
     * @return mixed
     */
    public function update(Request $request,  $id)
    {
        $account = Account::find($id);
        $account->fill($request->only(['token', 'status', 'on_server', 'group_id', 'role', 'profile']));
        $account->save();

        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function changePassword($id){
        $account = Account::find($id);
        $account->old_password = $account->password;
        $account->password = str_random(16);
        $account->token = null;
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function changeEmailPassword($id){
        $account = Account::find($id);
        $account->email_password = str_random(10);
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function generateToken($id){
        $account = Account::find($id);
        if($account->on_server['value']){
            $data = file_get_contents('http://'.config('facebook.token_server').'/get_token.php?u='.
                $account->fb_id.'&p='.$account->password.'&browser='.base64_encode($account->browser?$account->browser->name:''));

            $data = \GuzzleHttp\json_decode($data, true);
        }else
            $data = Facebook::generateToken($account);

        $account->token = $data['access_token']??($data['error']['message']??$data['error_msg']);
        $account->login_info = json_encode($data);
        if(isset($data['access_token'])){
            $account->status = 1;

            //update token for pages
            if($account->role['value'] == 2){ //editor
                $pages = Facebook::getPages($account->token);

                $myPages = MyPage::where('group_id', $account->group->id)
                    ->get();

                foreach($myPages as $myPage){
                    $myPage->token = $pages[$myPage->fb_id]['access_token'];
                    $myPage->save();
                }
            }
        }
        else
            $account->status = 0;
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function updateInfo($id){
        $account = Account::find($id);
        $user = Facebook::getUser('me', $account->token);
        if(isset($user['id'])){
            $account->email = $user['email'];
            $account->first_name = $user['first_name'];
            $account->last_name = $user['last_name'];
            $account->middle_name = $user['middle_name']??null;
            $account->friends = $user['friends']['summary']['total_count'];
            $account->fb_id = $user['id'];
            $tmp = explode('/', $user['birthday']);
            $account->birthday = $tmp[2].'-'.$tmp[0].'-'.$tmp[1];
            $account->country = $user['location']['location']['country']??null;
            if(isset($user['gender']))
                $account->gender = $user['gender'] == 'male'?2:1;

            $account->status = 1;
            $account->save();

            //update token for pages
            if($account->role['value'] == 2){ //editor
                $pages = Facebook::getPages($account->token);

                $myPages = MyPage::where('group_id', $account->group->id)
                    ->get();

                foreach($myPages as $myPage){
                    $myPage->token = $pages[$myPage->fb_id]['access_token'];
                    $myPage->save();
                }
            }
        }else{
            $account->status = 0;
            $account->token = Arr::get($user, 'error.message');
            $account->save();
        }


        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function backupFriends($id){
        $account = Account::find($id);
        $photos = Facebook::getFriendsTaggedPhotos('me', $account->token);
        Storage::disk('local')->put('backup/'.$account->fb_id.'.txt', json_encode($photos));
        $account->backup = date('Y-m-d H:i:s');
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function backupAllFriends(){
        $accounts = Account::where('status', 1)
            ->whereNull('backup')
            ->where('on_server', 0)
            ->where('id', '>', 69)
            ->get();
        foreach($accounts as $account){
            $photos = Facebook::getFriendsTaggedPhotos('me', $account->token);
            Storage::disk('local')->put('backup/'.$account->fb_id.'.txt', json_encode($photos));
            $account->backup = date('Y-m-d H:i:s');
            $account->save();
        }
    }

    public function viewFriends($id){
        $account = Account::find($id);
        $friendsData = $account->backup?json_decode(Storage::get('backup/'.$account->fb_id.'.txt'), true):[];
        return view('admin.accounts.friends', ['account' => $account, 'friendsData' => $friendsData]);
    }
}
