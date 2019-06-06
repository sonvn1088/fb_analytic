<?php

namespace App\Console\Commands;

use App\Helps\General;
use App\Helps\Facebook;
use App\Helps\Simthue;
use Illuminate\Console\Command;

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
        General::sharePosts(70, 70);
        //Facebook::refreshInstantArticles('EAADLtAvpCMIBADZCZAr15E09q6CKKYosVXttZAZBHSyVoBm94eZA8J7bZA8Mt5kMGbpUhzrqcopIZAvh35D5y3HNaByaKCTNxpZC7X0bjq4V9lrD99itZC9J0Rjf9hZCcHSmNBFjYRv7NnYGIopsOs1ZBtmAXAcUfaogA8c9dZBVnfoMb5iJNnLZBK5RHFc76BkRUMmPClkarKRYL8gZDZD');
        /*$myPage = \App\Models\MyPage::find(92);
        $posts = Facebook::getPublishedPosts($myPage->token, time() - 7*30*24*3600, null, 2000);
        foreach($posts as $id => $post){
            Facebook::delete($id, $myPage->token);
        }*/
    }
}