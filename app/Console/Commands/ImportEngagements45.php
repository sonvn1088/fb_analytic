<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helps\Import;

class ImportEngagements45 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:engagements45';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import engagements after 45 minutes from pages';

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
        Import::engagements(45);
    }
}
