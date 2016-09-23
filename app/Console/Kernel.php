<?php

namespace App\Console;

use App\Console\Commands\ArticleImages;
use App\Console\Commands\GenerateJSPlayerList;
use App\Console\Commands\HudsonvilleAthleticsParser;
use App\Console\Commands\MLiveParser;
use App\Console\Commands\MWPARankingCommand;
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
        GenerateJSPlayerList::class,
        ArticleImages::class,
        MWPARankingCommand::class,
        HudsonvilleAthleticsParser::class,
        MLiveParser::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }
}
