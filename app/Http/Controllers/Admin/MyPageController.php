<?php

namespace App\Http\Controllers\Admin;


use App\Helps\Facebook;
use App\Models\Account;
use App\Models\Group;
use App\Models\MyPage;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Yajra\Datatables\Datatables;

class MyPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.my_pages.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Datatables::of(MyPage::query())
            ->editColumn('group_id', function ($myPage){
                return $myPage->group?$myPage->group->name:'';
            })
            ->editColumn('status', function ($myPage){
                return $myPage->status['label'];
            })
            ->editColumn('editor', function ($myPage){
                if($editor = $myPage->editor())
                    return Arr::only($editor->toArray(), ['first_name', 'last_name', 'id', 'profile']);
                else
                    return [];
            })
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Group::all();
        $groupsData = ['' => '--'];
        foreach($groups as $group){
            $groupsData[$group->id] = $group->name;
        }

        $sites = Site::all();
        foreach($sites as $site){
            $sitesData[$site->id] = $site->name;
        }

        return view('admin.my_pages.show', ['myPage' => new MyPage(), 'groups' => $groupsData, 'sites' => $sitesData]);
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
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $myPage = MyPage::find($id);
        if($myPage){
            $groups = Group::all();
            $groupsData = ['' => '--'];
            foreach($groups as $group){
                $groupsData[$group->id] = $group->name;
            }

            $sites = Site::all();
            foreach($sites as $site){
                $sitesData[$site->id] = $site->name;
            }

            return view('admin.my_pages.show', ['myPage' => $myPage, 'groups' => $groupsData, 'sites' => $sitesData]);
        }

        else
            return redirect()->intended(route('admin.my_pages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function save(Request $request,  $id)
    {
        $myPage = $id?MyPage::find($id):new MyPage();

        $data = $request->only(['fb_id', 'name', 'like', 'follow', 'username', 'status', 'group_id', 'site_ids']);
        $myPage->fill($data);

        if(!$id){
            $info = Facebook::getPageInfos($myPage->fb_id);
            $myPage->fill($info);
        }

        $myPage->save();

        return redirect()->intended(route('admin.my_pages.show', $id));
    }
}
