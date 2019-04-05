<?php

namespace App\Http\Controllers;

use App\Helps\Import;
use App\Helps\Links;

class PageController extends Controller
{
    public function viewTopLinks(){
        return Links::getTopLinks();
    }

    public function importEngagements($time){
        Import::engagements($time);
    }

    public function importPosts(){
        Import::posts();
    }

    public function importPages(){
        Import::pages();
    }

}
