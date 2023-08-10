<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('exam:corn')->dailyAt('05:00');
        $schedule->command('fees:cron')
                ->dailyAt('12:00');
        $schedule->command('lesson:status')->yearlyOn(9,1);
        $schedule->command('lesson:notification')->dailyAt('0:0');
        $schedule->command('report:mfees')->monthlyOn(25,'08:00');
        $schedule->command('report:yfees')->yearlyOn(10,10,'08:00');

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
