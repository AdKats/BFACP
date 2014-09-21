<?php namespace ADKGamers\Webadmin\Controllers;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Battlelog;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Libs\Reputation;
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommand;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Chatlog;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Requests;
use WebadminException, Exception, Requests_Exception;
use Zizaco\Confide\Facade AS Confide;
use Zizaco\Entrust\EntrustFacade AS Entrust;

class PlayerController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Shows the player information
     * @param  integer $id  Player DB ID
     * @param  string $name Not required but used for SEO
     */
    public function showInfo($id = NULL, $name = '')
    {
        if(empty($id))
        {
            return View::make('error.generror')->with('code', 400)->with('errmsg', 'INVALID REQUEST')
                    ->with('errdescription', 'Missing required parameters to perform function')->with('title', 'Invalid Request');
        }

        if(!$player = Player::find($id))
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PLAYER NOT FOUND')
                    ->with('errdescription', 'Could not locate player in database')->with('title', 'Player Not Found');
        }

        $data['bf3'] = Server::bf3()->get();
        $data['bf4'] = Server::bf4()->get();

        $_gameIdent = Helper::getGameName($player->GameID);

        if(!empty($player->ClanTag))
        {
            $title = sprintf("[%s] %s - Player Profile", $player->ClanTag, $player->SoldierName);
        } else $title = sprintf("%s - Player Profile", $player->SoldierName);

        $_cmds = AdKatsCommand::where('command_active', 'Active')->whereIn('command_logging', ['Log', 'Mandatory'])
                        ->orderBy('command_name')->get();

        View::share('title', $title);

        $this->layout->content = View::make('public.battlefield.common.playercard')
                ->with('player', $player)
                ->with('servers', $data)
                ->with('_forgive', Entrust::can('issueforgive'))
                ->with('_gameIdent', $_gameIdent)
                ->with('_cmds', $_cmds);
    }

    public function externalRequests($id)
    {
        $player     = Player::find($id);
        $_gameIdent = Helper::getGameName($player->GameID);

        $_bf4db     = NULL;
        $_battlelog = NULL;

        if($_gameIdent == 'BF4')
        {
            try
            {
                $_query = http_build_query(array(
                    'format' => 'json',
                    'guid' => $player->EAGUID
                ));

                $response = Requests::get("http://api.bf4db.com/api-player.php?" . $_query);

                $response_decode = json_decode($response->body, true);

                if($response_decode['type'] == 'error' && $response_decode['message'] == 'PLAYER_NOT_FOUND')
                    throw new WebadminException("Player Not Found");

                $url = explode('/', parse_url($response_decode['data']['bf4db_url'], PHP_URL_PATH));

                $_bf4db['id'] = intval($url[2]);
                $_bf4db['response'] = $response_decode['data'];
            }
            catch(Requests_Exception $e) {}
            catch(WebadminException $e) {}
        }

        try
        {
            $_blog = new Battlelog($player);

            $_battlelog = $_blog->saveToDB();
        }
        catch(WebadminException $e) {}

        $data['battlelog'] = $_battlelog;
        $data['bf4db']     = $_bf4db;
        $data['player']    = $player->SoldierName;
        $data['game']      = $_gameIdent;

        return Helper::response('success', NULL, $data);
    }

    public function chartHistory($id)
    {
        $spline_chart = DB::select(file_get_contents(storage_path() . '/sql/player_cmd_history_spline.sql'), array($id));
        $pie_chart    = DB::select(file_get_contents(storage_path() . '/sql/player_overview_chart.sql'), array($id));

        $pie_chart_ip = Record::where('command_type', 49)->where('record_message', '!=', 'No previous IP on record')->where('target_id', $id)
                            ->select(DB::raw("record_message AS 'ip', COUNT(record_id) AS 'total'"))
                            ->groupBy('record_message')->get();

        $pie_chart_name = Record::where('command_type', 48)->where('target_id', $id)
                            ->select(DB::raw("record_message AS 'pname', COUNT(record_id) AS 'total'"))
                            ->groupBy('record_message')->get();

        $data['splinechart']       = array();
        $data['piechart']          = array();
        $data['piechart_ips']      = array();
        $data['piechart_soldiers'] = array();

        $i = 0;
        foreach(AdKatsCommand::all() as $command)
        {
            foreach($spline_chart as $result)
            {
                if($command->command_name == $result->command_name)
                {
                    $cmdname  = $result->command_name;
                    $cmdyear  = intval($result->Year);
                    $cmdmonth = intval($result->Month);
                    $cmdday   = intval($result->Day);
                    $cmdtotal = intval($result->total);

                    $date = Carbon::create($cmdyear, $cmdmonth, $cmdday, 0, 0, 0);

                    $data['splinechart'][$i]['name'] = $cmdname;
                    $data['splinechart'][$i]['data'][] = array(
                        strtotime($date) * 1000,
                        $cmdtotal
                    );
                }
            }

            if(array_key_exists($i, $data['splinechart'])) $i++;
        }

        foreach($pie_chart as $key => $row)
        {
            if(intval($row->value) == 0) continue;
            $pie_chart[$key]->value = intval($row->value);

            $data['piechart'][] = array(
                $row->label,
                $row->value
            );
        }

        foreach($pie_chart_ip as $result)
        {
            $data['piechart_ips'][] = array(
                $result->ip,
                intval($result->total)
            );
        }

        foreach($pie_chart_name as $result)
        {
            $data['piechart_soldiers'][] = array(
                $result->pname,
                intval($result->total)
            );
        }

        return Helper::response('success', NULL, $data);
    }

    public function showInfoRecords($id)
    {
        if(!Input::has('type'))
        {
            return Helper::response('error', 'No search type specified');
        }

        $query = Record::select('adkats_records_main.*', 'tbl_server.ServerName')
                        ->join('tbl_server', 'adkats_records_main.server_id', '=', 'tbl_server.ServerID')
                        ->whereNotIn('command_type', [48, 49])
                        ->orderBy('record_time', 'desc');

        switch(Input::get('type'))
        {
            case "on":
                $query->where('target_id', $id);
            break;

            case "by":
                $query->where('source_id', $id);
            break;

            default:
                return Helper::response('error', 'Invalid search type');
        }

        if(Input::get('filter_command') != 'none' && !is_null(Input::get('filter_command')))
        {
            $query->where(function($query)
            {
                $query->where('command_type', Input::get('filter_command'));
                $query->orWhere('command_action', Input::get('filter_command'));
            });
        }

        $results = $query->paginate(20)->toArray();

        foreach(AdKatsCommand::all() as $command)
        {
            $commands[$command->command_id] = $command->command_name;
        }

        foreach($results['data'] as $key => $record)
        {
            if(in_array($record['command_type'], [7, 72]) || in_array($record['command_action'], [7, 72]))
            {
                $dt_start = Carbon::createFromFormat('Y-m-d H:i:s', $record['record_time']);
                $difference = $dt_start->diffInSeconds($dt_start->copy()->addMinutes($record['command_numeric']));
                $results['data'][$key]['command_numeric'] = Helper::convertSecToStr($difference);
            }
            else
            {
                $results['data'][$key]['command_numeric'] = NULL;
            }

            $results['data'][$key]['record_time']    = Helper::UTCToLocal($record['record_time'])->format('M j, Y g:ia T');
            $results['data'][$key]['command_type']   = $commands[$record['command_type']];
            $results['data'][$key]['command_action'] = $commands[$record['command_action']];
            $results['data'][$key]['adkats_web']     = (bool) $results['data'][$key]['adkats_web'];

            if(!is_null($record['target_id']))
                $results['data'][$key]['target_link'] = action("ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo", array($record['target_id'], $record['target_name']));
            if(!is_null($record['source_id']))
                $results['data'][$key]['source_link'] = action("ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo", array($record['source_id'], $record['source_name']));
        }

        return Helper::response('success', NULL, $results);
    }

    public function getRep($id)
    {
        try
        {
            $player = Player::find($id);

            $rep = new Reputation;
            $rep->setPlayer($player);
            $rep->createOrUpdateOnly();

            $result = $player->reputation;
            $result->target_rep   = intval($result->target_rep);
            $result->source_rep   = intval($result->source_rep);
            $result->total_rep    = intval($result->total_rep);
            $result->total_rep_co = intval($result->total_rep_co);

            return Helper::response('success', NULL, $result);
        }
        catch(Exception $e)
        {
            return Helper::response('error', $e->getMessage());
        }
    }

    public function getPlayerStats($id)
    {
        $player_sum_stats = DB::select(file_get_contents(storage_path() . '/sql/player_stats.sql'), [$id]);

        $player_wep_stats = DB::select(file_get_contents(storage_path() . '/sql/player_weapon_stats.sql'), [$id]);

        $player_sessions  = DB::select(file_get_contents(storage_path() . '/sql/player_session_stats.sql'), [$id]);

        foreach($player_wep_stats as $key => $pss)
        {
            $player_wep_stats[$key]->Kills     = intval($pss->Kills);
            $player_wep_stats[$key]->Headshots = intval($pss->Headshots);
            $player_wep_stats[$key]->Deaths    = intval($pss->Deaths);
        }

        $sessions = [];

        foreach($player_sessions as $key => $session)
        {
            $sessions[] = array(
                intval($session->SessionID),
                Helper::UTCToLocal($session->StartTime)->format('M j, Y g:ia T'),
                Helper::UTCToLocal($session->EndTime)->format('M j, Y g:ia T'),
                $session->Score,
                $session->HighScore,
                intval($session->Kills),
                intval($session->Deaths),
                intval($session->Headshots),
                intval($session->Suicide),
                intval($session->TKs),
                intval($session->Wins),
                intval($session->Losses),
                intval($session->RoundCount),
                Helper::convertSecToStr($session->Playtime, TRUE),
                $session->ServerName
            );
        }

        $pdata = array(
            'summary' => array_filter((array)$player_sum_stats[0], 'strlen'),
            'sessions' => $sessions,
            'weapons' => $player_wep_stats
        );

        return Helper::response('success', NULL, $pdata);
    }

    public function getChatLog($id)
    {
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

        $query = Chatlog::where('logPlayerID', $id)->whereNotIn('logMessage', $comoRoseCode)
                    ->join('tbl_server', 'tbl_chatlog.ServerID', '=', 'tbl_server.ServerID')
                    ->select(DB::raw('tbl_chatlog.*, tbl_server.ServerName'))
                    ->orderBy('logDate', 'desc');

        if(Input::has('filter_server_id'))
        {
            $inputServer = Input::get('filter_server_id');

            if(is_numeric($inputServer))
            {
                $query->where('tbl_chatlog.ServerID', intval($inputServer));
            }
        }

        if(Input::has('filter_message_string'))
        {
            $query->where('logMessage', 'LIKE', '%' . Input::get('filter_message_string') . '%');
        }

        $chatlog = $query->paginate(20)->toArray();

        foreach($chatlog['data'] as $key => $entry)
        {
            $chatlog['data'][$key]['logDate'] = Helper::UTCToLocal($entry['logDate'])->format('M j, Y g:ia T');
        }

        return Helper::response('success', NULL, $chatlog);
    }

    public function forgive($id)
    {
        if(!Entrust::can('issueforgive'))
            return Helper::response('error', 'Access Denied! You do not have correct permissions to do this action');

        $player = Player::find($id);
        $preferences = Auth::user()->preferences;
        $gameID = Helper::getGameName($player->GameID);

        $times = Input::get('xtimes', 1);
        $server = Input::get('server');
        $message = Input::get('message', 'ForgivePlayer');

        if(!Input::has('server'))
        {
            return Helper::response('error', 'No server id provided');
        }

        switch($gameID)
        {
            case "BF3":
                if(is_null($preferences->bf3_playerid))
                {
                    $source_player_name = Auth::user()->username;
                    $source_player_id = NULL;
                }
                else
                {
                    $sourcePlayerQuery = Player::where('GameID', $player->GameID)->where('PlayerID', $preferences->bf3_playerid)->first();
                    $source_player_name = $sourcePlayerQuery->SoldierName;
                    $source_player_id = $sourcePlayerQuery->PlayerID;
                }
            break;

            case "BF4":
                if(is_null($preferences->bf4_playerid))
                {
                    $source_player_name = Auth::user()->username;
                    $source_player_id = NULL;
                }
                else
                {
                    $sourcePlayerQuery = Player::where('GameID', $player->GameID)->where('PlayerID', $preferences->bf4_playerid)->first();
                    $source_player_name = $sourcePlayerQuery->SoldierName;
                    $source_player_id = $sourcePlayerQuery->PlayerID;
                }
            break;

            default:
                $source_player_name = Auth::user()->username;
                $source_player_id = NULL;
            break;
        }

        $msg = sprintf("You have forgivin %s %u time(s). Refresh page to see changes", $player->SoldierName, $times);
        $class = 'success';

        $infraction = DB::table('adkats_infractions_server')->where('player_id', $player->PlayerID)->where('server_id', $server)->first();

        if($times > $infraction->punish_points && $infraction->forgive_points != $infraction->punish_points)
        {
            $times_old = $times;

            $times = $infraction->punish_points;

            $msg = sprintf(
                "You have forgivin %s %u time(s) but was reduced to %u to equal max punishes. Issue the remaining %u points on another server if possable.",
                $player->SoldierName,
                $times_old,
                $infraction->punish_points,
                abs($infraction->punish_points - $times)
            );

            $class = 'warning';
        }
        else if($infraction->forgive_points == $infraction->punish_points)
        {
            return Helper::response('error', 'You cannot forgive a player if they do not have any punishes to forgive for this server');
        }

        for($i = 0; $i < $times; $i++)
        {
            $record = new Record;
            $record->server_id       = $server;
            $record->command_type    = 10;
            $record->command_action  = 10;
            $record->command_numeric = 0;
            $record->target_name     = $player->SoldierName;
            $record->target_id       = $player->PlayerID;
            $record->source_name     = $source_player_name;
            $record->source_id       = $source_player_id;
            $record->record_message  = $message;
            $record->record_time     = Carbon::now();
            $record->adkats_read     = 'Y';
            $record->adkats_web      = TRUE;
            $record->save();
        }

        return Helper::response('success', $msg, ['class' => $class]);
    }
}
