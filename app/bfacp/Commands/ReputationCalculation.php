<?php namespace BFACP\Commands;

use BFACP\Battlefield\Player;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ReputationCalculation extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfacp:repcal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates all players reputations.';

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
        DB::connection()->disableQueryLog();

        $r = App::make('BFACP\Libraries\Reputation');

        $this->info("Counting up total players.");

        $count = Player::where('GameID', '>', 0)->count();

        $this->info(sprintf('Found %s players.', number_format($count)));

        $chunk = 20000;

        Player::where('GameID', '>', 0)->chunk($chunk, function ($players) use (&$r) {
            foreach ($players as $player) {
                try {
                    $startClock = microtime(true) * 1000;
                    $r->setPlayer($player)->createOrUpdate()->resetValues();
                    $endClock = microtime(true) * 1000;

                    $execClock = $endClock - $startClock;

                    $txt = sprintf("[%u][%u ms] %s", $player->PlayerID, $execClock, $player->SoldierName);

                    $this->info("Updated player: " . $txt);
                } catch (Exception $e) {
                    $this->error('Update failed. Reason: ' . $e->getMessage());
                }
            }
        });
    }

}
