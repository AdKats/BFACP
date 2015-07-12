<?php namespace BFACP\Commands;

use Illuminate\Console\Command;

class Upgrade extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfacp:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the BFACP to the latest version';

    /**
     * Create a new command instance.
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
        //
    }

}
