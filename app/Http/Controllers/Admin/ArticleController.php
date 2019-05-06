<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.articles.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $sites = Site::where('status', Site::ENABLED)
            ->get();

        $items = [];
        foreach($sites as $site){
            $items = array_merge($items, json_decode(file_get_contents($site->path), true));
        }

        $articles = collect($items)->map(function ($item) {
            $item['created_time'] = Carbon::parse($item['created_time'], 'GMT')
                ->diffForHumans();
            return (object) $item;
        });
        return Datatables::of($articles)
            ->make(true);
    }
}

