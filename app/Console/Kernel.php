<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        for($i = 1; $i <= 11; $i++ ){
            $command = 'share:posts'.($i<10?'0'.$i:$i);
            $schedule->command($command)->everyThirtyMinutes();
        }

        $schedule->command('import:posts')->everyTenMinutes();
        $schedule->command('export:topLinks')->everyFifteenMinutes();
        $schedule->command('import:engagements15')->everyMinute();
        $schedule->command('import:engagements30')->everyMinute();
        $schedule->command('import:engagements45')->everyMinute();
        $schedule->command('import:engagements60')->everyMinute();
        $schedule->command('import:engagements90')->everyMinute();
        $schedule->command('import:engagements120')->everyMinute();
        $schedule->command('import:engagements180')->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
