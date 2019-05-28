<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helps\Links;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;

class ExportTopLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:topLinks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export top link to file';

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
        $links = Links::getTopLinks(Page::VN);
        Storage::put('public/links.txt', json_encode($links));

        $links = Links::getTopLinks(Page::TH);
        Storage::put('public/th_links.txt', json_encode($links));
    }
}
