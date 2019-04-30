<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helps\General;

class SharePosts04 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:posts04';

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
        General::sharePosts(31, 40);
    }
}
