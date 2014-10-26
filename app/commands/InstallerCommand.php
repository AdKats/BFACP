<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Server;

class InstallerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfadmincp:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the installer to setup and configure the BFAdminCP';

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
        $this->info("Thank you for choosing to install the Battlefield Admin Control Panel.");
        $this->info("You will be asked a few questions to help configure your install.\n");


        /*=================================
        =            Variables            =
        =================================*/

        $missing_php54  = FALSE;
        $missing_mcrypt = FALSE;
        $missing_pdo    = FALSE;
        $servers_to_add = [];
        $utr_key        = NULL;
        $use_utr        = FALSE;
        $use_bf3        = FALSE;
        $use_bf4        = FALSE;
        $use_ssl        = FALSE;
        $use_auth       = FALSE;


        /*-----  End of Variables  ------*/

        /*==================================
        =            Check List            =
        ==================================*/

        $this->info("Step 1: Pre-check list");
        $this->info("----------------------------------------\n");

        $this->info("PHP Version Check...");

        if(version_compare(phpversion(), '5.4.0', '<'))
        {
            $missing_php54 = TRUE;
            $this->error("Failed!........You do not meet the PHP 5.4 requirement. You're running version " . phpversion());
        }
        else
        {
            $this->info("Passed!");
        }

        $this->info("\nMCrypt Extension Installed & Enabled...");

        if(extension_loaded("mcrypt"))
        {
            $this->info("Passed!");
        }
        else
        {
            $missing_mcrypt = TRUE;
            $this->error("Failed!........You are missing the Mcrypt extension or is not enabled");
        }

        $this->info("\nPDO Database Driver Installed & Enabled...");

        if(extension_loaded("pdo"))
        {
            $this->info("Passed!");
        }
        else
        {
            $missing_pdo = TRUE;
            $this->error("Failed!........You are missing the PDO Database Driver or is not enabled");
        }

        if($missing_pdo || $missing_mcrypt || $missing_php54)
        {
            $this->info("\n\nPlease fix the errors in red and then rerun the installer.");
            die();
        }


        /*-----  End of Check List  ------*/

        $this->info("\nStep 2: Configuration");
        $this->info("----------------------------------------\n");

        /*==========================================
        =            Step 2.1 - General            =
        ==========================================*/

        $this->info("Step 2.1");
        $this->info("----------------------------------------\n");

        $this->info("Leave blank to use default value.");
        $use_bf3  = $this->confirm("Do you want to enable the Battlefield 3 section? (Default: Yes) [yes|no]", true);
        $use_bf4  = $this->confirm("Do you want to enable the Battlefield 4 section? (Default: Yes) [yes|no]", true);
        $use_ssl  = $this->confirm("Do you want to force SSL connections? (Default: No) [yes|no]", false);
        $use_auth = $this->confirm("Do you only want registered users to be able to access the BFAdminCP? (Default: No) [yes|no]", false);

        /*-----  End of Step 2.1 - General  ------*/


        /*=================================================
        =            Section 2.2 - UptimeRobot            =
        =================================================*/

        $this->info("\nStep 2.2 - UptimeRobot");
        $this->info("----------------------------------------\n");

        if($use_utr = $this->confirm("Do you want to use Uptime Robot? [yes|no]"))
        {
            $utr_key = $this->ask("Please enter your Main API key from UptimeRobot.com: ");

            if($this->confirm("Would you like to add your servers now? [yes|no]"))
            {
                $this->info("You will be presented with each server and asked if it should be included on UptimeRobot.\n");

                $servers = Server::orderBy('ServerName', 'asc')->get();

                $this->info(sprintf("I found %u %s.", $servers->count(), Lang::choice('server|servers', $servers->count()) ) );

                $this->info("\nPress enter to use default option. The default option is No.\n");

                foreach($servers as $server)
                {
                    $msg = sprintf("Would you like to add [%s] %s (%s) to UptimeRobot? [Yes|No]",
                                Helper::getGameName($server->GameID),
                                str_limit($server->ServerName, 35, '...'),
                                Helper::getIpAddr($server->IP_Address)
                            );

                    if($this->confirm($msg)) $servers_to_add[] = $server;
                }
            }
        }

        /*-----  End of Section 2.2 - UptimeRobot  ------*/

    }
}
