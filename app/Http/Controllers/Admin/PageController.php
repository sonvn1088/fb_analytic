<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Helps\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Datatables::of(Page::query())
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.show', ['page' => new Page()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = Page::find($id);
        if($page){
            return view('admin.pages.show', ['page' => $page]);
        }

        else
            return redirect()->intended(route('admin.pages'));
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
        $page = $id?Page::find($id):new Page();
        $data = $request->only(['fb_id', 'name', 'like', 'follow', 'username', 'type']);
        $page->fill($data);

        if(!$id){
            $info = Facebook::getPageInfos($page->fb_id?:$page->username);
            $page->fill($info);
        }

        $page->save();

        return redirect()->intended(route('admin.pages.show', $page->id));
    }
    
    public function import(){
        $array = [

        ];

        foreach($array as $item){
            $page = Page::firstOrNew(['username' => $item]);
            $info = Facebook::getPageInfos($page->username);
            $info['likes'] = $info['like'];
            $page->fill($info);

            $page->save();

        }
    }
}

