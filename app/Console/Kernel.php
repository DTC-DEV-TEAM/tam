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
        /** TIMFS **/
        // $schedule->call('\App\Http\Controllers\AdminAssetsController@getItemMasterTimfsData')->hourly()->between('9:00', '21:00');
        // $schedule->call('\App\Http\Controllers\AdminAssetsController@getItemMasterUpdatedTimfsData')->hourly()->between('9:00', '21:00');

        /** DAM **/
        $schedule->call('\App\Http\Controllers\AdminAssetsItController@getItemMasterDataDamApi')->hourly()->between('9:00', '21:00');
        $schedule->call('\App\Http\Controllers\AdminAssetsItController@getItemMasterUpdatedDataDamApi')->hourly()->between('9:00', '21:00');

        /** DAM Categories */
        // $schedule->call('\App\Http\Controllers\AdminCategoriesController@getCategoriesDataApi')->hourly()->between('9:00', '21:00');
        // $schedule->call('\App\Http\Controllers\AdminCategoriesController@getCategoriesUpdatedDataApi')->hourly()->between('9:00', '21:00');

        /** DAM Class */
        // $schedule->call('\App\Http\Controllers\AdminClassesController@getClassCreatedDataApi')->hourly()->between('9:00', '21:00');
        // $schedule->call('\App\Http\Controllers\AdminClassesController@getClassUpdatedDataApi')->hourly()->between('9:00', '21:00');
        
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
