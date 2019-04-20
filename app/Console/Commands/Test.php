<?php

namespace App\Console\Commands;


use App\Helps\General;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test';

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
        General::parseArticle('https://quiznhe.com/khanh-sky-se-thay-the-de-phat-1-kieu-toc-tro-thanh-trend-moi-cho-gioi-tre-412689.html');
    }
}
