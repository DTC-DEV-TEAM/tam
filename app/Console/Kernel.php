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
        'App\Console\Commands\DatabaseBackup',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('\App\Http\Controllers\AdminItemsController@getItemsUpdatedAPI')->hourly()->between('9:00', '21:00');
        $schedule->call('\App\Http\Controllers\AdminItemsController@getItemsCreatedAPI')->hourly()->between('9:00', '21:00');
        $schedule->call('\App\Http\Controllers\AdminOrderSchedulesController@deactivateSchedule')->dailyAt('04:00');
        $schedule->call('\App\Http\Controllers\AdminPurchaseOrderController@closeHeaders')->everyMinute();
        $schedule->command('mysql:backup')->daily()->at('20:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
