<?php

namespace App\Http\Controllers;

use App\Helps\Import;


class PageController extends Controller
{
    public function viewLinks(){

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
