<?php namespace BFACP\Commands;

use BFACP\Adkats\Infractions\Overall;
use Illuminate\Console\Command;

class InfractionsCleanup extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfacp:infractions-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up infractions';

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
        Overall::with('player', 'servers')->where('total_points', '<', 0)->chunk(1000, function ($infractions) {
            foreach ($infractions as $infraction) {
                $total = abs($infraction->total_points);

                if ($infraction->forgive_points > 0 && $infraction->punish_points == 0) {
                    $total = $infraction->forgive_points;
                } elseif ($infraction->forgive_points > $infraction->punish_points) {
                    $total = $infraction->forgive_points - $infraction->punish_points;
                }

                $this->info(sprintf('Deleted %s forgives for %s', $total, $infraction->player->SoldierName));
                sleep(0.5);
            }
        });
    }

}
