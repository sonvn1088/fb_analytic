<?php

namespace App\Http\Controllers\Admin;



use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Yajra\Datatables\Datatables;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = Group::all();
        return view('admin.groups.index', ['groups' => $groups]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Datatables::of(Group::query())
            ->addColumn('accounts', function($group){
                return $group->accounts->map(function ($item) {
                    return $item->only(['first_name', 'last_name', 'id', 'profile', 'role']);
                })->toArray();
            })
            ->addColumn('pages', function($group){
                return $group->pages->map(function ($item) {
                    return $item->only(['name', 'id', 'status', 'fb_id']);
                })->toArray();
            })
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.groups.show', ['group' => new Group()]);
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
        $group = Group::find($id);
        if($group)
            return view('admin.groups.show', ['group' => $group]);
        else
            return redirect()->intended(route('admin.groups'));
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
        $group = $id?Group::find($id):new Group();

        $data = $request->only(['name']);
        $group->fill($data);
        $group->save();

        return redirect()->intended(route('admin.groups'));
    }

    public function import(){

    }
}
