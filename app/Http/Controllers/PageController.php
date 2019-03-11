<?php

namespace App\Http\Controllers;

use App\Helps\Import;
use App\Helps\Facebook;

class PageController extends Controller
{
    public function viewLinks(){

    }

    public function importEngagements($time){
        $engagement = Facebook::getEngagement('378003969315198_632709590511300');
        print_r($engagement);die();
        //Import::engagements($time);
    }

    public function importPosts(){
        Import::posts();
    }

    public function importPages(){
        Import::pages();
    }

}
