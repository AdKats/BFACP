<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfadmincp:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs clean up functions and other optimisations';

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
    public function fire()
    {
        // Update bans that have expired
        $bans_updated = Event::fire("ban.update.expired");

        $this->info(sprintf("%u ban(s) were updated", head($bans_updated)));

        // Remove any infractions which are all zeros
        $infractions_deleted = Event::fire("cleanup_infractions");

        $this->info(sprintf("%u infraction(s) with all zeros were deleted", head($infractions_deleted)));
    }
}
