<?php

namespace App\Http\Controllers;

use App\Helps\General;
use App\Helps\Import;
use App\Helps\Links;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request){
        return General::parseArticle($request->get('url'));
    }


}
