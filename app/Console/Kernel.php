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
        \App\Console\Commands\SendReport::class,
        // \App\Console\Commands\RabbitConsumer::class,
        // \App\Console\Commands\RabbitConsumerExchange::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send-report')->everyTenMinutes();

        // $schedule->command('rabbit:consume')->everyMinute();
        // $schedule->command('rabbit:exchange')->everyMinute();
    }
}
