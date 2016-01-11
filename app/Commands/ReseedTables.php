<?php namespace BFACP\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ReseedTables extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfacp:reseed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseeds certain tables without destroying existing data';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if ($this->confirm('Are you sure you want to reseed the tables? [yes|no]')) {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Truncate permissions table
            DB::table(Config::get('entrust::permissions_table'))->truncate();
            DB::table(Config::get('entrust::permission_role_table'))->truncate();

            // Reseed the permissions table
            $this->call('db:seed', ['--force' => true, '--class' => 'PermissionsTableSeeder']);
            $this->info('Permission table reseeded. You will need to setup your roles permissions again.');

            // Enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('Tables were successfully reseeded!');
        } else {
            $this->info('Tables were not reseeded.');
        }
    }

}
