<?php

namespace BFACP\Console;

use BFACP\Console\Commands\InfractionsCleanup;
use BFACP\Console\Commands\ReputationCalculation;
use BFACP\Console\Commands\ReseedTables;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ReseedTables::class,
        InfractionsCleanup::class,
        ReputationCalculation::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
