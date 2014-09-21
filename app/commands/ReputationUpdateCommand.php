<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use ADKGamers\Webadmin\Models\Battlefield\Player;

class ReputationUpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfadmincp:reputation-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all players reputations';

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
        DB::connection()->disableQueryLog();
        $r = new ADKGamers\Webadmin\Libs\Reputation;

        $this->info("Counting up total players.");

        $count = Player::where('GameID', '>', 0)->count();

        $this->info("Found " . number_format($count) . " players");

        $chunk = 20000;

        $batch = Helper::divide($count, $chunk, 0);

        Player::where('GameID', '>', 0)->chunk($chunk, function($players) use(&$r)
        {
            foreach($players as $player)
            {
                $startClock = microtime(true) * 1000;
                $r->setPlayer($player);
                $r->createOrUpdateOnly();
                $endClock = microtime(true) * 1000;

                $execClock = $endClock - $startClock;

                $txt = sprintf("[%u][%u ms] %s", $player->PlayerID, $execClock, $player->SoldierName);

                $this->info("Updated player: " . $txt);
            }
        });
    }
}
