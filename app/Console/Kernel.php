<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ClearPublicStorage;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\GuestCredit;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClearPublicStorage::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('storage:clear')->dailyAt('00:00');

        $schedule->call(function () {
            GuestCredit::truncate();
        })->daily();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
