<?php namespace ADKGamers\Webadmin\Libs;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Reputation AS Rep;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use Illuminate\Support\Facades\DB;
use Requests, WebadminException, Exception;
use Carbon\Carbon;

class Reputation
{
    const URL = "https://raw.githubusercontent.com/ColColonCleaner/AdKats/master/adkatsreputationstats.json";

    private $_weights = [];

    private $player = NULL;

    public function __construct()
    {
        $this->_fetchWeights();
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }

    private function calculateSourceReputation($breakdown = FALSE)
    {
        $start = 0;

        $results = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))
                        ->where('source_id', $this->player->PlayerID)
                        ->whereRaw('target_name <> source_name')
                        ->groupBy('command_type')
                        ->groupBy('command_action')->get();

        foreach($results as $result)
        {
            $command = sprintf("%u|%u", $result->command_type, $result->command_action);
            $commandCount = intval($result->command_count);

            foreach($this->_weights as $weight)
            {
                if($command == $weight['command_typeaction'])
                {
                    $start += $weight['source_weight'] * $commandCount;

                    if($breakdown)
                    {
                        $temp[] = array(
                            'command_type' => $result->command_type,
                            'command_action' => $result->command_action,
                            'command_typeaction' => $command,
                            'weight' => array(
                                'source' => $weight['source_weight'],
                                'target' => $weight['target_weight']
                            ),
                            'total' => $commandCount,
                            'reputation_earned' => ($weight['source_weight'] * $commandCount)
                        );
                    }

                    break;
                }
            }
        }

        return $breakdown ? ['breakdown' => $temp, 'value' => $start] : $start;
    }

    private function calculateTargetReputation($breakdown = FALSE)
    {
        $start = 0;
        $startPoint = 0;

        $recentPunishments = Record::where('command_type', 9)->where('target_id', $this->player->PlayerID)->get();

        foreach($recentPunishments as $punishment)
        {
            $timeSince = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $punishment->record_time));

            if($timeSince < 50)
            {
                $startPoint -= 20 * ( ( 50 - $timeSince ) / 50 );
            }
        }

        $recentForgives = Record::where('command_type', 10)->where('target_id', $this->player->PlayerID)->get();

        foreach($recentForgives as $forgive)
        {
            $timeSince = Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $forgive->record_time));

            if($timeSince < 50)
            {
                $startPoint += 20 * ( ( 50 - $timeSince ) / 50 );
            }
        }

        if($startPoint > 0) $startPoint = 0;

        $start = $startPoint;

        $results = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))
                        ->where('target_id', $this->player->PlayerID)
                        ->whereRaw('target_name <> source_name')
                        ->groupBy('command_type')
                        ->groupBy('command_action')->get();

        foreach($results as $result)
        {
            $command = sprintf("%u|%u", $result->command_type, $result->command_action);
            $commandCount = $result->command_count;

            foreach($this->_weights as $weight)
            {
                if($command == $weight['command_typeaction'])
                {
                    $start += $weight['target_weight'] * $commandCount;

                    if($breakdown)
                    {
                        $temp[] = array(
                            'command_type' => $result->command_type,
                            'command_action' => $result->command_action,
                            'command_typeaction' => $command,
                            'weight' => array(
                                'source' => $weight['source_weight'],
                                'target' => $weight['target_weight']
                            ),
                            'total' => $commandCount,
                            'reputation_earned' => ($weight['target_weight'] * $commandCount)
                        );
                    }

                    break;
                }
            }
        }

        return $breakdown ? ['breakdown' => $temp, 'value' => $start] : $start;
    }

    private function calculateSpecialReputation($breakdown = FALSE)
    {
        $start = [
            'source' => 0,
            'target' => 0
        ];

        $results = Record::select(DB::raw('command_type, command_action, COUNT(record_id) AS command_count'))
                        ->where('source_id', $this->player->PlayerID)
                        ->where('target_id', $this->player->PlayerID)
                        ->where('command_type', 51)
                        ->where('command_action', 51)
                        ->groupBy('command_type')
                        ->groupBy('command_action')->get();

        foreach($results as $result)
        {
            $command = sprintf("%u|%u", $result->command_type, $result->command_action);
            $commandCount = intval($result->command_count);

            foreach($this->_weights as $weight)
            {
                if($command == $weight['command_typeaction'])
                {
                    $start['source'] += $weight['source_weight'] * $commandCount;
                    $start['target'] += $weight['target_weight'] * $commandCount;
                    break;
                }
            }
        }

        return $start;
    }

    public function get()
    {
        $special = $this->calculateSpecialReputation();
        $source  = $this->calculateSourceReputation() + $special['source'];
        $target  = $this->calculateTargetReputation() + $special['target'];
        $total   = $this->calculate($source + $target);

        return Helper::response('success', 'Reputation results', array(
            'source_rep'   => $source,
            'target_rep'   => $target,
            'total_rep'    => $source + $target,
            'total_rep_co' => $total
        ));
    }

    public function calculateOnly($breakdown = FALSE)
    {
        $special = $this->calculateSpecialReputation();
        $source  = $this->calculateSourceReputation($breakdown);
        $source['value'] + $special['source'];

        $target  = $this->calculateTargetReputation($breakdown);
        $target['value'] + $special['target'];

        if($breakdown)
        {
            $total = $this->calculate($source['value'] + $target['value']);
        }
        else
        {
            $total = $this->calculate($source + $target);
        }

        return array(
            'special' => $special,
            'source'  => $source,
            'target'  => $target,
            'total'   => $total
        );
    }

    public function createOrUpdateOnly()
    {
        $special = $this->calculateSpecialReputation();
        $source  = $this->calculateSourceReputation() + $special['source'];
        $target  = $this->calculateTargetReputation() + $special['target'];
        $total   = $this->calculate($source + $target);
        $this->update($source, $target, $total);
    }

    private function update($source, $target, $total)
    {
        $playerRep = Rep::find($this->player->PlayerID);

        if(!$playerRep)
        {
            $newPlayerRep               = new Rep;
            $newPlayerRep->player_id    = $this->player->PlayerID;
            $newPlayerRep->game_id      = $this->player->GameID;
            $newPlayerRep->source_rep   = $source;
            $newPlayerRep->total_rep    = $target + $source;
            $newPlayerRep->total_rep_co = $total;
            $newPlayerRep->save();

            return;
        }

        $playerRep->target_rep   = $target;
        $playerRep->source_rep   = $source;
        $playerRep->total_rep    = $target + $source;
        $playerRep->total_rep_co = $total;

        $playerRep->save();
    }

    protected function calculate($val)
    {
        if($val < 0)
        {
            $neg = TRUE;
            $val = abs($val);
        }

        $result = (1000 * $val) / ( $val + 1000 );

        if(isset($neg) && $neg) $result = -$result;

        return $result;
    }

    private function _fetchWeights()
    {
        try
        {
            $response = Requests::get(self::URL);

            $this->_weights = json_decode($response->body, true);
        }
        catch(Requests_Exception $e) {}
    }
}
