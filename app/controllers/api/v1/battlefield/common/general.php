<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield\Common;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Battlefield AS BFHelper;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommand;
use ADKGamers\Webadmin\Models\Battlefield\Chatlog;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class General extends \BaseController
{
    /**
     * Live Scoreboard Chat
     * @param  integer $id Server ID
     * @return array
     */
    public function getScoreboardChat($id)
    {
        $chat = Chatlog::where('ServerID', $id)->where('logDate', '>=', Carbon::today());

        $comoRoseCode = array(
            'ID_CHAT_REQUEST_MEDIC',
            'ID_CHAT_REQUEST_AMMO',
            'ID_CHAT_THANKS',
            'ID_CHAT_REQUEST_RIDE',
            'ID_CHAT_AFFIRMATIVE',
            'ID_CHAT_GOGOGO',
            'ID_CHAT_SORRY',
            'ID_CHAT_ATTACK/DEFEND',
            'ID_CHAT_REQUEST_ORDER',
            'ID_CHAT_GET_IN',
            'ID_CHAT_NEGATIVE',
            'ID_CHAT_GET_OUT',
            'ID_CHAT_REQUEST_REPAIRS'
        );

        $results = $chat->whereNotIn('logMessage', $comoRoseCode)->whereNotNull('logPlayerID')->orderBy('logDate', 'desc')->take(30)->get();

        foreach($results as $key => $result)
        {
            $results[$key]->ID = intval($result->ID);
            $results[$key]->logPlayerID = intval($result->logPlayerID);
            $results[$key]->logDateIso = $result->logDate->toISO8601String();
            $results[$key]->profile = action("ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo", array($result->logPlayerID, $result->logSoldierName));
        }

        return Helper::response('success', NULL, $results->toArray());
    }

    public function getScoreboard($server_id = NULL)
    {
        // Check if server id was givin
        if(is_null($server_id))
            return Helper::response('error', 'Server ID not provided');

        // Check if $server_id is numeric
        if(!is_numeric($server_id))
            return Helper::response('error', 'The ID "' . $server_id . '" is not a number');

        // Deny request if id is less than 1
        if($server_id < 1)
            return Helper::response('error', 'Server ID cannot be a negative value or less than 1');

        $lsb = new Scoreboard(Server::with('setting')->find($server_id));

        return $lsb->get();
    }

    public function postReports()
    {
        $query = Record::whereIn('command_type', [18, 20])
                ->where('record_time', '>=', Carbon::parse('-1 hour'));

        if(Input::has("last_report_id") && !is_null(Input::get('last_report_id')))
        {
            $results = $query->where('record_id', '>', Input::get('last_report_id'))->orderBy('record_id', 'asc')->get();
        }
        else
        {
            $results = $query->orderBy('record_id', 'asc')->get();
        }

        $data = [];

        foreach($results as $result)
        {
            $server = Server::find($result->server_id);

            $data[] = array(
                'record_id' => intval($result->record_id),
                'id'        => intval($result->command_numeric),
                'type_id'   => intval($result->command_type),
                'action_id' => intval($result->command_action),
                'target'    => $result->target_name,
                'target_id' => intval($result->target_id),
                'source'    => $result->source_name,
                'source_id' => intval($result->source_id),
                'message'   => $result->record_message,
                'timestamp' => strtotime($result->record_time),
                'server' => array(
                    'short' => is_null($server->setting) ? NULL : $server->strip($server->setting->name_strip),
                    'full'  => $server->ServerName,
                    'id'    => intval($server->ServerID)
                )
            );
        }

        return Helper::response('success', NULL, $data);
    }

    public function getPlayerMaps()
    {
        $results = Player::select(DB::raw('CountryCode, COUNT(PlayerID) Total'))->whereNotNull('CountryCode')
                        ->where('CountryCode', '!=', '')
                        ->where('CountryCode', '!=', '--')
                        ->groupBy('CountryCode')->get();

        $temp = [];

        foreach($results as $result)
        {
            $region = Helper::countries($result->CountryCode);

            if(is_null($region)) continue;

            $temp[] = array(
                'code'  => strtoupper($result->CountryCode),
                'value' => intval($result->Total),
                'name'  => $region
            );
        }

        return Helper::response('success', NULL, $temp);
    }

    public function postRoundstats()
    {
        $server_id = Input::get('server_id', NULL);
        $round_id = Input::get('round_id', NULL);

        if(is_null($server_id) || !is_numeric($server_id))
            return Helper::response('error', 'Invalid server id');

        if(is_null($round_id) || !is_numeric($round_id))
            return Helper::response('error', 'Invalid round id');

        if(Auth::check())
        {
            $_tz = Auth::user()->preferences->timezone;
        }
        else $_tz = 'UTC';

        $results = DB::table('tbl_extendedroundstats')->where('server_id', abs($server_id))->where('round_id', abs($round_id))->orderBy('roundstat_time')->get();

        $temp = array(
            array(
                'name' => 'Team 1 TPM',
                'type' => 'spline',
                'data' => [],
                'tooltip' => ['valueSuffix' => ' Tickets Per Min']
            ),
            array(
                'name' => 'Team 1 Tickets',
                'type' => 'spline',
                'data' => []
            ),
            array(
                'name' => 'Team 2 TPM',
                'type' => 'spline',
                'data' => [],
                'tooltip' => ['valueSuffix' => ' Tickets Per Min']
            ),
            array(
                'name' => 'Team 2 Tickets',
                'type' => 'spline',
                'data' => [],
            ),
        );

        foreach($results as $result)
        {
            $datetime = Helper::UTCToLocal($result->roundstat_time, $_tz)->toDateTimeString();

            $datetime_epoch = strtotime($datetime) * 1000;

            $temp[0]['data'][] = [$datetime_epoch, $result->team1_tickets + $result->team1_tpm];
            $temp[1]['data'][] = [$datetime_epoch, $result->team1_tickets];
            $temp[2]['data'][] = [$datetime_epoch, $result->team2_tickets + $result->team2_tpm];
            $temp[3]['data'][] = [$datetime_epoch, $result->team2_tickets];
        }

        return Helper::response('success', NULL, $temp);
    }
}
