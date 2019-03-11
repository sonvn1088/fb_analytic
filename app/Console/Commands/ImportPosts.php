<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helps\Import;
use App\Helps\Facebook;
use App\Models\Post;

class ImportPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import posts from pages';

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
        Import::posts();

        $posts = Post::all();
        foreach($posts as $post){
            if(!$post->message){
                $f_post = Facebook::getPostInfo($post->post_id);
                $post->message = $f_post['message'];
                $post->save();
            }
        }
    }
}
