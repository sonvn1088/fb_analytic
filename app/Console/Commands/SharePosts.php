<?php

namespace App\Console\Commands;

use App\Helps\Facebook;
use App\Helps\General;
use App\Models\Account;
use App\Models\Browser;
use Illuminate\Console\Command;
use App\Models\MyPage;
use Illuminate\Support\Arr;

class SharePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share posts to pages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $myPages = MyPage::where('status', MyPage::ENABLED)->get();
        //$myPages = MyPage::whereIn('id', [13])->get();
        foreach($myPages as $myPage){
            $result = Facebook::checkToken($myPage->token);
            if(isset($result['id']))
                $myPage->sharePosts();
            else{
                $editor = $myPage->editor();
                if($editor){
                    $editor->status = Account::INACTIVE;
                    $editor->save();
                    $editor->error_message = Arr::get($result, 'error.message');
                    General::sendMail($editor);
                }
            }
        }
    }
}
