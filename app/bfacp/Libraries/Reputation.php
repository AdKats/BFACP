<?php namespace BFACP\Libraries;

use BFACP\Adkats\Record;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Reputation
{
    /**
     * Player Object
     *
     * @var Player
     */
    public $player;

    /**
     * Source reputation
     *
     * @var integer
     */
    public $sourceReputation = 0;

    /**
     * Target reputation
     *
     * @var integer
     */
    public $targetReputation = 0;

    /**
     * Sum of source and target reputation
     *
     * @var integer
     */
    public $totalReputation = 0;

    /**
     * Real reputation
     *
     * @var integer
     */
    public $finalReputation = 0;

    /**
     * Guzzle Client
     *
     * @var Client
     */
    protected $guzzle;

    /**
     * Array of weights
     *
     * @var array
     */
    protected $weights = [];

    public function __construct()
    {
        $this->guzzle = App::make('GuzzleHttp\Client');
        $this->fetchWeights();
    }

    /**
     * Fetch the reputation weights and cache them for 1 day
     *
     * @return mixed
     */
    private function fetchWeights()
    {
        $this->weights = Cache::remember('reputation.weights', 60 * 24, function () {
            try {
                $request = $this->guzzle->get('https://raw.githubusercontent.com/AdKats/AdKats/master/adkatsreputationstats.json');
            } catch (\Exception $e) {
                $request = $this->guzzle->get('http://api.gamerethos.net/adkats/fetch/reputation');
            }

            return $request->json();
        });

        return $this;
    }

    /**
     * Set the player
     *
     * @param Player $player
     *
     * @return $this
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Function to reset the values in the case of a loop being used.
     *
     * @return $this
     */
    public function resetValues()
    {
        $this->sourceReputation = 0;
        $this->targetReputation = 0;
        $this->totalReputation = 0;
        $this->finalReputation = 0;

        return $this;
    }

    /**
     * Creates or Updates the player reputation then reloads it
     *
     * @return mixed
     */
    public function createOrUpdate()
    {
        $this->source()->target()->special()->calculate();

        if ($this->player->hasReputation()) {
            $reputation = $this->player->reputation;

            // Only update the reputation if the total reputation is different
            if ($reputation->total_rep != $this->totalReputation) {
                $reputation->source_rep = $this->sourceReputation;
                $reputation->target_rep = $this->targetReputation;
                $reputation->total_rep = $this->totalReputation;
                $reputation->total_rep_co = $this->finalReputation;
                $reputation->save();

                // Reload the relationship
                $this->player->load('reputation');
            }
        } else {
            $this->player->reputation()->save(new \BFACP\Battlefield\Reputation([
                'game_id'      => $this->player->GameID,
                'source_rep'   => $this->sourceReputation,
                'target_rep'   => $this->targetReputation,
                'total_rep'    => $this->totalReputation,
                'total_rep_co' => $this->finalReputation,
            ]));

            // Reload the relationship
            $this->player->load('reputation');
        }

        return $this;
    }

    /**
     * Fetches records by player to calculate the source reputation
     *
     * @return mixed
     */
    public function source()
    {
        $records = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))->where('source_id',
            $this->player->PlayerID)->whereRaw('target_name != source_name')->groupBy('command_type')->groupBy('command_action')->get();

        foreach ($records as $record) {
            $command = sprintf('%u|%u', $record->command_type, $record->command_action);

            foreach ($this->weights as $weight) {
                if ($command == $weight['command_typeaction']) {
                    $this->sourceReputation += $weight['source_weight'] * $record->command_count;
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Fetches records targeted on player to calculate the target reputation
     *
     * @return mixed
     */
    public function target()
    {
        // Retrieve the punish records
        $punishments = $this->player->recordsOn()->where('command_type', 9)->get();

        foreach ($punishments as $punishment) {
            $days = Carbon::now()->diffInDays($punishment->record_time);

            if ($days < 50) {
                $this->targetReputation -= 20 * MainHelper::divide(50 - $days, 50);
            }
        }

        // Retrieve the forgive records
        $forgives = $this->player->recordsOn()->where('command_type', 10)->get();

        foreach ($forgives as $forgive) {
            $days = Carbon::now()->diffInDays($forgive->record_time);

            if ($days < 50) {
                $this->targetReputation += 20 * MainHelper::divide(50 - $days, 50);
            }
        }

        // Retrieve the rest
        $records = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))->where('target_id',
            $this->player->PlayerID)->whereRaw('target_name != source_name')->groupBy('command_type')->groupBy('command_action')->get();

        foreach ($records as $record) {
            $command = sprintf('%u|%u', $record->command_type, $record->command_action);

            foreach ($this->weights as $weight) {
                if ($command == $weight['command_typeaction']) {
                    $this->targetReputation += $weight['target_weight'] * $record->command_count;
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Fetches special records to be applied to the reputation values
     *
     * @return mixed
     */
    public function special()
    {
        $records = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))->where('source_id',
            $this->player->PlayerID)->where('target_id', $this->player->PlayerID)->where('command_type',
            51)->where('command_action', 51)->groupBy('command_type')->groupBy('command_action')->get();

        foreach ($records as $record) {
            $command = sprintf('%u|%u', $record->command_type, $record->command_action);

            foreach ($this->weights as $weight) {
                if ($command == $weight['command_typeaction']) {
                    $this->sourceReputation += $weight['source_weight'] * $record->command_count;
                    $this->targetReputation += $weight['target_weight'] * $record->command_count;
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Calculates the total/final reputation
     *
     * @return mixed
     */
    public function calculate()
    {
        $this->totalReputation = $this->sourceReputation + $this->targetReputation;

        // Save the total reputation into $value so we can modify it
        $value = $this->totalReputation;

        // If the number is negative then this will be true
        $negative = false;

        // Check if we have a negative value
        if ($value < 0) {
            $negative = true;
            $value = abs($value);
        }

        $newValue = (1000 * $value) / ($value + 1000);

        // If the value is negative we need to make
        // sure to set it as a negative number
        if ($negative) {
            $newValue = -$newValue;
        }

        $this->finalReputation = $newValue;

        return $this;
    }
}
