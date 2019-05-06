<?php

namespace App\Http\Controllers\Admin;


use App\Helps\Facebook;
use App\Helps\General;
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
                return $myPage->status;
            })
            ->editColumn('accounts', function ($myPage){
                $accounts = $myPage->group->accounts;
                return $accounts->map(function ($item, $key) {
                    return Arr::only($item->toArray(), ['first_name', 'last_name', 'id', 'profile', 'role']);
                })->toArray();
            })
            ->editColumn('scheduled_posts', function ($myPage){
               $result = Facebook::get($myPage->fb_id, ['fields' => 'scheduled_posts'], $myPage->token);
                if(isset($result['scheduled_posts']['data']))
                    return count($result['scheduled_posts']['data']);
                elseif(isset($result['error']))
                    return 0;
                else
                    return 0;
            })
            ->editColumn('published_posts', function ($myPage){
                $result = Facebook::getPublishedPosts($myPage->token, time() - 3600);
                if(!isset($result['error']))
                    return count($result);
                else
                    return 0;
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
        if($request->get('blocked_at'))
            $blockedAt = \DateTime::createFromFormat(config('general.format_time'), $request->get('blocked_at'))
                ->format('Y-m-d H:i:s');
        else
            $blockedAt = null;

        $data = $request->only(['fb_id', 'name', 'like', 'follow', 'username', 'status', 'group_id', 'site_ids', 'query_track']);
        $data['blocked_at'] = $blockedAt;
        $myPage->fill($data);


        if(!$id){
            $info = Facebook::getPageInfos($myPage->fb_id);
            $myPage->fill($info);
        }

        $myPage->save();

        return redirect()->intended(route('admin.my_pages.show', $myPage->id));
    }

    public function check(){
        return Datatables::of(MyPage::query()->where('status, 1'))
            ->editColumn('scheduled_posts', function ($myPage){
                $result = Facebook::get($myPage->fb_id, ['fields' => 'scheduled_posts'], $myPage->token);
                return Arr::get($result, 'scheduled_posts.data');
            })

            ->make(true);

    }

    public function openPage($id){
        $myPage = MyPage::find($id);
        $editor = $myPage->editor();
        if($editor)
            General::openProfile($editor->profile, $myPage->getManageScheduledPostUrl());
    }



}
