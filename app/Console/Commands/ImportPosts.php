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
        $posts = Post::all();
        foreach($posts as $post){
            if(strpos($post->post_id, '_')){
                $tmp = explode('_', $post->post_id);
                $post->post_id = $tmp[1];
                $post->page_id = $tmp[0];
                $post->save();
            }
        }

        Import::posts();
    }
}
