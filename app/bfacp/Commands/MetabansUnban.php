<?php namespace BFACP\Commands;

use BFACP\Battlefield\Player;
use BFACP\Exceptions\MetabansException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;

class MetabansUnban extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bfacp:mbunban';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows you to remove a ban from metabans.';

    private $questions = [
        'Q1' => 'What is the ID of the player you wish to unban? ',
        'Q2' => 'Is this the correct player? [%s] %s. [yes|no] ',
        'Q3' => 'What is the reason for unbanning? (Leave blank to use default): ',
        'Q4' => '%s will be unbanned from metabans with the reason of "%s". Continue? [yes|no] ',
    ];

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
        $_playerFound = false;

        try {
            $metabans = App::make('BFACP\Libraries\Metabans');
        } catch (MetabansException $e) {
            $this->error('Failed to initialize the Metabans Library.');
            die;
        }

        do {
            $playerID = $this->ask($this->questions['Q1']);
            try {
                if (!is_numeric($playerID)) {
                    throw new \InvalidArgumentException();
                }

                $player = Player::findOrFail($playerID);

                $question = sprintf($this->questions['Q2'], $player->game->Name,
                    $player->SoldierName);

                if ($this->confirm($question, false)) {
                    $_playerFound = true;

                    if (!is_null($metabans)) {
                        $unbanReason = $this->ask($this->questions['Q3']);

                        if (empty($unbanReason)) {
                            $unbanReason = 'Unbanned';
                        }

                        $question2 = sprintf($this->questions['Q4'],
                            $player->SoldierName, $unbanReason);

                        if ($this->confirm($question2, false)) {
                            $metabans->assess($player->game->Name, $player->EAGUID, 'None', $unbanReason);
                            $this->info(sprintf('%s should now have been unbanned on metabans. Verify that it went through.',
                                $player->SoldierName));
                        } else {
                            $this->info('Request canceled!');
                        }
                    }
                }
            } catch (ModelNotFoundException $e) {
                $this->error(sprintf('Could not find the player with the ID "%s". Please try again.', $playerID));
            } catch (\InvalidArgumentException $e) {
                $this->error(sprintf('Only integers are allowed for the player id. Input was: %s', gettype($playerID)));
            } catch (MetabansException $e) {
                $this->error('Failed to initialize the Metabans Library.');
                die;
            }
        } while ($_playerFound == false);
    }

}
