<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use ADKGamers\Webadmin\Models\Battlefield\Player;

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
        $this->info("
                ___  ____ ____ ___  _  _ _ _  _ ____ ___     _ _  _ ____ ___ ____ _    _    ____ ____
                |__] |___ |__| |  \ |\/| | |\ | |    |__]    | |\ | [__   |  |__| |    |    |___ |__/
                |__] |    |  | |__/ |  | | | \| |___ |       | | \| ___]  |  |  | |___ |___ |___ |  \

            ");

        $this->info("Thank you for choosing to install the Battlefield Admin Control Panel (BFAdminCP).");
        $this->info("You will be asked a few questions to help configure your install.\n\n");


        /*=================================
        =            Variables            =
        =================================*/

        $missing_php54 = FALSE;
        $missing_mcrypt = FALSE;
        $missing_pdo = FALSE;

        /*-----  End of Variables  ------*/



        /*==================================
        =            Check List            =
        ==================================*/

        $this->info("Step 1: Pre-check list\n");

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





        /*
        $user                        = new User;
        $user->username              = 'Admin';
        $user->email                 = 'admin@example.com';
        $user->password              = 'password';
        $user->password_confirmation = 'password';
        $user->confirmed             = TRUE;
        $user->save();

        $preferences = Preference::create(['user_id' => $user->id]);

        $user->attachRole($newrole);
        */
    }
}
