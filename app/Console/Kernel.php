<?php

namespace App\Console;

use App\Jobs\TimetableCreate;
use App\Jobs\TimetableNoticeEnd;
use App\Jobs\TimetableNoticeStart;
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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            TimetableNoticeEnd::handle();
        })->dailyAt('19:00');
        $schedule->call(function () {
            TimetableNoticeStart::handle();
        })->dailyAt('09:31');
        $schedule->call(function () {
            TimetableCreate::handle();
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
