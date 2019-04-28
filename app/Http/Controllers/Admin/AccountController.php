<?php

namespace App\Http\Controllers\Admin;


use App\Helps\Facebook;
use App\Helps\Yahoo;
use App\Models\Account;
use App\Models\App;
use App\Models\Browser;
use App\Models\Group;
use App\Models\MyPage;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['generateAppToken']]);
    }

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

            $apps = App::all();
            $appsData = ['' => '--'];
            foreach($apps as $app){
                $appsData[$app->id] = $app->name;
            }
            return view('admin.accounts.show', ['account' => $account, 'groups' => $groupsData, 'apps' => $appsData]);
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
        $account->fill($request->only(['token', 'status', 'on_server', 'group_id', 'role', 'profile', 'app_id']));
        $account->save();

        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function changePassword($id){
        $account = Account::find($id);
        $account->old_password = $account->password;
        $account->password = str_random(16);
        $account->token = null;
        $account->app_token = null;
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
            $account->status = Account::ACTIVE;

            //update token for pages
            if($account->role['value'] == Account::EDITOR){ //editor
                $account->setPagesToken(true);
            }
        }
        else
            $account->status = Account::INACTIVE;
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function updateInfo($id){
        $account = Account::find($id);
        $user = Facebook::getUser('me', $account->token?:$account->app_token);

        if(isset($user['id'])){
            if(isset($user['friends'])){
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
            }

            $account->status = Account::ACTIVE;
            $account->save();

            //update token for pages
            if($account->role['value'] == Account::EDITOR){ //editor
                $account->setPagesToken($account->app_token?false:true);
            }
        }else{
            $account->status = Account::INACTIVE;
            $account->token = Arr::get($user, 'error.message');
            $account->save();
        }


        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function backupFriends($id){
        $account = Account::find($id);
        $photos = [];
        if($account->token)
            $photos = Facebook::getFriendsTaggedPhotos('me', $account->token);
        elseif($account->friend_with){
            $friendAccount = Account::find($account->friend_with);
            $photos = Facebook::getFriendsTaggedPhotos($account->fb_id, $friendAccount->token);
        }

        Storage::disk('local')->put('backup/'.$account->fb_id.'.txt', json_encode($photos));
        $account->backup = date('Y-m-d H:i:s');
        $account->save();
        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function viewFriends($id){
        $account = Account::find($id);
        $friendsData = $account->backup?json_decode(Storage::get('backup/'.$account->fb_id.'.txt'), true):[];
        return view('admin.accounts.friends', ['account' => $account, 'friendsData' => $friendsData]);
    }

    public function openAppToGetToken($accountId){
        $account = Account::find($accountId);
        $app = $account->app;

        $params = [
            'client_secret' => $app->secret,
            'account_id' => $accountId,
            'client_id' => $app->key,
        ];

        $data = base64_encode(http_build_query($params));

        exec('"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --profile-directory="Profile '.$account->profile.
            '" '.config('facebook.app_token_url').'?data='.$data);
    }

    public function scanAccounts($id){
        $account = Account::find($id);
        $friends = Facebook::getFriends('me', $account->token);

        foreach($friends as $friend) {
            if (isset($friend['email']) && strpos($friend['email'], 'yahoo') && !strpos($friend['email'], 'yahoo.com.vn')) {
                $notExits = Yahoo::checkEmail($friend['email']);
                if($notExits) {
                    $birthday = Arr::get($friend, 'birthday', '01/01/1970');
                    if(strlen($birthday) < 6){
                        $birthday .= '/1970';
                    }
                    $friend['birthday'] = Carbon::createFromFormat('m/d/Y', $birthday)->format('Y-m-d');
                    $friend['gender'] = Arr::get($friend, 'gender') == 'male'?2:1;
                    $friend['fb_id'] = $friend['id'];
                    $friend['friend_with'] = $id;
                    $friend['browser_id'] = Browser::inRandomOrder()->first()->id;
                    $newAccount = Account::firstOrNew(['fb_id' => $friend['id']]);
                    $newAccount->fill($friend);
                    $newAccount->save();
                }
            }
        }

        $account->scanned_at = date('Y-m-d H:i:s');
        $account->save();

        return redirect()->intended(route('admin.accounts.show', $id));
    }

    public function delete($id){
        $account = Account::find($id);
        $account->delete();
        return redirect()->intended(route('admin.accounts'));
    }
}
