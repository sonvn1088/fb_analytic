<?php

namespace App\Console\Commands;

use App\Helps\General;

class SharePosts05 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:posts05';

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
        General::sharePosts(41, 50);
    }
}
