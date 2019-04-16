<?php

namespace App\Http\Controllers\Admin;

use App\Models\App;
use App\Helps\Facebook;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.apps.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Datatables::of(App::query())
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.apps.show', ['app' => new App()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $app = App::find($id);
        if($app){
            return view('admin.apps.show', ['app' => $app]);
        }

        else
            return redirect()->intended(route('admin.apps'));
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
        $app = $id?App::find($id):new App();
        $data = $request->only(['name', 'key', 'secret']);
        $app->fill($data);
        $app->save();

        return redirect()->intended(route('admin.apps.show', $app->id));
    }
}

