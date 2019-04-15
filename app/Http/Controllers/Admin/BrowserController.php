<?php

namespace App\Http\Controllers\Admin;

use App\Models\Browser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class BrowserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.browsers.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Datatables::of(Browser::query())
            ->make(true);
    }

    public function import(){
        $content = file_get_contents('http://'.config('facebook.token_server').'/browser_agent.txt');

        foreach(preg_split("/((\r?\n)|(\r\n?))/", $content) as $line){
            $type = 0;
            if(strpos($line, 'Android'))
                $type = 1;
            elseif(strpos($line, 'iPhone'))
                $type = 2;
            elseif(strpos($line, 'iPad'))
                $type = 3;

            if(in_array($type, [1,2,3]) && strlen($line) <= 255 && strpos($line, '[FB'))
                $browser = Browser::firstOrCreate(['name' => $line, 'type' => $type]);
        }

        return redirect()->intended(route('admin.browsers'));
    }



}

