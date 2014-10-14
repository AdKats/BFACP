<?php namespace ADKGamers\Webadmin\Controllers;

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
use ADKGamers\Webadmin\Libs\UptimeRobotAPI;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Reputation;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Models\Battlefield\Setting AS GSetting;
use Carbon\Carbon, Requests, WebadminException, Exception, User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Roumen\Feed\Facades\Feed;

class PublicController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Show the dashboard
     */
    public function showIndex()
    {
        View::share('title', Lang::get('navigation.public.dashboard'));

        if(Config::get('webadmin.BF3') == 1)
        {
            $bf3Id = Helper::getGameId('BF3');

            $bf3Bans = Ban::select('ban_id', 'player_id', 'target_name', 'source_name', 'source_id', 'ban_status', 'ban_startTime', 'ban_endTime', 'record_message', 'target_id')
                ->join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                ->join('tbl_playerdata', 'adkats_records_main.target_id', '=', 'tbl_playerdata.PlayerID')
                ->where('GameID', $bf3Id)
                ->where('ban_status', 'Active')
                ->orderBy('ban_startTime', 'desc')->take(30)->get();
        }

        if(Config::get('webadmin.BF4') == 1)
        {
            $bf4Id = Helper::getGameId('BF4');

            $bf4Bans = Ban::select('ban_id', 'player_id', 'target_name', 'source_name', 'source_id', 'ban_status', 'ban_startTime', 'ban_endTime', 'record_message', 'target_id')
                ->join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                ->join('tbl_playerdata', 'adkats_records_main.target_id', '=', 'tbl_playerdata.PlayerID')
                ->where('GameID', $bf4Id)
                ->where('ban_status', 'Active')
                ->orderBy('ban_startTime', 'desc')->take(30)->get();
        }

        $userTz = 'UTC';

        if(Auth::check())
        {
            $userTz = Auth::user()->preferences->timezone;
        }

        $yesterdayBanCount = Ban::where('ban_startTime', '>=', Carbon::yesterday($userTz))
                            ->where('ban_startTime', '<=', Carbon::today($userTz))->count();

        $playerCountNow = Server::where('ConnectionState', 'on')->sum('usedSlots');

        $avgBansPerDay = head(DB::select(File::get(storage_path() . '/sql/avg_bans_per_day.sql')));



        $this->layout->content = View::make('public.index')
            ->with('bans', array( 'bf3' => ( isset( $bf3Bans ) ? $bf3Bans : [] ), 'bf4' => ( isset( $bf4Bans ) ? $bf4Bans : [] ) ) )
            ->with('stats', array('bans' => $yesterdayBanCount, 'players' => $playerCountNow, 'bansPerDay' => $avgBansPerDay));
    }

    /**
     * Player searching
     */
    public function searchForPlayer()
    {
        $player      = trim(Input::get('player', NULL));
        $urlbuild    = NULL;
        $query_build = array();

        $players = Player::join('tbl_games', 'tbl_playerdata.GameID', '=', 'tbl_games.GameID')
                        ->select('tbl_playerdata.PlayerID', 'SoldierName', 'Name AS GameName');

        if(preg_match("/^(EA_)/As", $player))
        {
            if(strlen($player) == 35)
            {
                $players->where('EAGUID', $player);
            }
            else
            {
                return View::make('error.generror')->with('title', 'Search Error')->with('code', 400)
                        ->with('errmsg', 'Search Error')->with('errdescription', FALSE)
                        ->withErrors(array('player' => 'Invalid EAGUID'));
            }
        }
        else if(preg_match("/((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(?![\\d])/is", $player))
        {
            $validator = Validator::make(['player' => strtolower($player)],
                ['player' => 'required|ip'],
                [
                    'required'   => 'The player name is required'
                ]);

            if($validator->fails())
                return View::make('error.generror')->with('title', 'Search Error')->with('code', 400)->with('errmsg', 'Search Error')->with('errdescription', FALSE)->withErrors($validator);

            $players->where('IP_Address', $player);

            $messages = $validator->messages();
        }
        else
        {
            $validator = Validator::make(['player' => strtolower($player)],
                ['player' => 'required|min:3|alpha_dash'],
                [
                    'alpha_dash' => 'The player name may only contain letters, numbers, underscores, and dashes.',
                    'min'        => 'The player name must be at least :min characters.',
                    'required'   => 'The player name is required'
                ]);

            if($validator->fails())
                return View::make('error.generror')->with('title', 'Search Error')->with('code', 400)->with('errmsg', 'Search Error')->with('errdescription', FALSE)->withErrors($validator);

            $players->where('SoldierName', 'LIKE', $player . '%');

            $messages = $validator->messages();
        }

        if(Input::has('only'))
        {
            $gameValidator = Validator::make(Input::all(), [
                ['only' => 'in:bf3,bf4']
            ]);

            if(!$gameValidator->fails())
            {
                $players->where('tbl_games.Name', strtoupper(Input::get('only')));

                $query_build['page']   = Input::get('page');
                $query_build['player'] = $player;
                $query_build['only']   = Input::get('only', '');
            }
        }

        if(Input::has('sort'))
        {
            $sortBy    = Input::get('sort');
            $sortOrder = Input::get('order', 'asc');

            $sortValidator = Validator::make(
                ['sort' => $sortBy, 'order' => $sortOrder],
                [
                    ['sort' => 'in:PlayerID,SoldierName,GameName'],
                    ['order' => 'in:asc,desc']
            ]);

            if(!$sortValidator->fails())
            {
                $players->orderBy($sortBy, strtoupper($sortOrder));

                $query_build['page']   = Input::get('page');
                $query_build['player'] = $player;
                $query_build['only']   = Input::get('only', '');
                $query_build['order']  = strtolower($sortOrder) == 'asc' ? 'desc' : 'asc';
                $query_build['only']   = Input::get('only', '');
            }
        }

        if(!empty($query_build))
        {
            $urlbuild = "?" . http_build_query($query_build);
        }

        $results = $players->paginate(20);

        if($results->count() == 1)
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', array($results[0]->PlayerID, $results[0]->SoldierName));
        }

        return View::make('public.playersearch')->with('title', "Search results for " . $player)
                ->with('results', $results)
                ->with('urlbuild', $urlbuild)
                ->with('querystring', str_ireplace(array('&only=bf3', '&only=bf4'), '', $_SERVER['QUERY_STRING']));
    }

    public function showServerStats()
    {
        $serverlisting = Server::where('ConnectionState', 'on')->get();

        if(Input::has('sid') && is_numeric( Input::get('sid') ))
        {
            $server = Server::find(Input::get('sid'));

            if($server->ConnectionState == 'off' || is_null($server->ConnectionState))
                return View::make('error.generror')->with('code', 404)->with('errmsg', 'SERVER NOT FOUND')
                            ->with('errdescription', 'This server does not exist or not enabled')
                            ->with('title', 'Server Not Found');

            $serverSettings = GSetting::find(Input::get('sid'));

            $results['serverinfo'] = $server;

            /*====================================
            =            Uptime Robot            =
            ====================================*/

            try
            {
                if(empty($serverSettings->uptime_robot_id))
                {
                    $results['stats']['uptime']['ms'] = [];
                    $results['stats']['uptime']['logs'] = [];
                }
                else
                {
                    $utr = new UptimeRobotAPI;

                    // Request information
                    $robotResponse = $utr->getMonitors([ $serverSettings->uptime_robot_id ]);

                    // Store the timezone offset
                    $results['stats']['uptime']['timezone'] = $robotResponse['timezone'];

                    // Parse the response
                    $robotResponse = $utr->parse($robotResponse);

                    // Pull the logs
                    $results['stats']['uptime']['logs']   = $robotResponse['logs'];
                    // Pull the ratio
                    $results['stats']['uptime']['ratio']  = $robotResponse['ratio'];
                    // Pull the current status
                    $results['stats']['uptime']['status'] = $robotResponse['status'];

                    // Loop over the response times and convert them for use in highcharts
                    foreach($robotResponse['ms'] as $ping)
                    {
                        $datetime = $ping['timestamp'] . ' ' . $results['stats']['uptime']['timezone'];
                        $results['stats']['uptime']['ms'][] = [
                            strtotime($datetime) * 1000, // Convert to javascript timestamp
                            intval($ping['value'])
                        ];
                    }

                    $logCount = count($results['stats']['uptime']['logs']);

                    $lastKey = end($results['stats']['uptime']['logs']);
                    $lastKey = key($lastKey);

                    foreach($results['stats']['uptime']['logs'] as $key => $log)
                    {
                        if($logCount == 1 || $lastKey == $key)
                        {
                            $_datetime = Carbon::createFromFormat('m/d/y H:i:s', $log['timestamp']);

                            $diffHour = Carbon::now()->diffInHours($_datetime);
                            $diffMin  = Carbon::now()->diffInMinutes($_datetime);

                            $results['stats']['uptime']['logs'][$key]['duration'] = (
                                    $diffHour > 0 ?
                                    Lang::choice('general.datetime.hour', $diffHour, ['date' => $diffHour]) :
                                    Lang::choice('general.datetime.minute', $diffMin, ['date' => $diffMin])
                                );
                        }
                        else
                        {
                            try
                            {
                                $_datetime = Carbon::createFromFormat('m/d/y H:i:s', $log['timestamp']);
                                $_prevDateTime = Carbon::createFromFormat('m/d/y H:i:s', $results['stats']['uptime']['logs'][$key - 1]['timestamp']);

                                $diffHour = $_prevDateTime->diffInHours($_datetime);
                                $diffMin  = $_prevDateTime->diffInMinutes($_datetime);

                                $results['stats']['uptime']['logs'][$key]['duration'] = (
                                    $diffHour > 0 ?
                                    Lang::choice('general.datetime.hour', $diffHour, ['date' => $diffHour]) :
                                    Lang::choice('general.datetime.minute', $diffMin, ['date' => $diffMin])
                                );
                            }
                            catch(Exception $e) {}
                        }
                    }

                    // Sort the array to correctly display the graph
                    foreach($results['stats']['uptime']['ms'] as $key => $row)
                        $timestamp[$key] = $row[0];

                    array_multisort($timestamp, SORT_ASC, $results['stats']['uptime']['ms']);
                }
            }
            catch(WebadminException $e)
            {
                $results['stats']['uptime']['ms']    = [];
                $results['stats']['uptime']['logs']  = [];
                $results['stats']['uptime']['error'] = $e->getMessage();
            }
            catch(Exception $e)
            {
                $results['stats']['uptime']['ms']    = [];
                $results['stats']['uptime']['logs']  = [];
                $results['stats']['uptime']['error'] = $e->getMessage();
            }


            /*-----  End of Uptime Robot  ------*/


            /*=================================
            =            Map Stats            =
            =================================*/

            try
            {
                $mapStats = DB::table('tbl_mapstats')->where('ServerID', $server->ServerID)
                            ->where('TimeMapLoad', '>=', Carbon::parse('-72 hours'))->get();

                switch($server->gameIdent())
                {
                    case "BF3":
                        $filePath = app_path() . "/thirdparty/bf3/mapNames.xml";
                        $filePath2 = app_path() . "/thirdparty/bf3/playModes.xml";
                    break;

                    case "BF4":
                        $filePath = app_path() . "/thirdparty/bf4/mapNames.xml";
                        $filePath2 = app_path() . "/thirdparty/bf4/playModes.xml";
                    break;
                }

                foreach($mapStats as $key => $row)
                {
                    $mapStats[$key]->MapName          = BFHelper::getMapName($row->MapName, $filePath);
                    $mapStats[$key]->Gamemode         = BFHelper::getPlaymodeName($row->Gamemode, $filePath2);
                    $mapStats[$key]->TimeMapLoad      = Helper::UTCToLocal($row->TimeMapLoad)->format('M j, Y \@ g:i:sa T');
                    $mapStats[$key]->TimeRoundStarted = Helper::UTCToLocal($row->TimeRoundStarted)->format('M j, Y \@ g:i:sa T');
                    $mapStats[$key]->TimeRoundEnd     = Helper::UTCToLocal($row->TimeRoundEnd)->format('M j, Y \@ g:i:sa T');
                }

                $results['stats']['maps'] = $mapStats;
            }
            catch(Exception $e)
            {
                $results['stats']['maps'] = [];
            }

            /*-----  End of Map Stats  ------*/

            /*====================================
            =            Server Stats            =
            ====================================*/

            try
            {
                $stats = DB::table('tbl_server_stats')->where('ServerID', $server->ServerID)->first();

                $results['stats']['server'] = $stats;
            }
            catch(Exception $e)
            {
                $results['stats']['server'] = [];
            }

            /*-----  End of Server Stats  ------*/

            /*========================================
            =            Population Chart            =
            ========================================*/

            try
            {
                $population = DB::select(File::get(storage_path() . '/sql/population_chart.sql'), [$server->ServerID]);

                foreach($population as $row)
                {
                    $dt = Carbon::create(intval($row->Year), intval($row->Month), intval($row->Day), intval($row->Hour));

                    $results['stats']['population'][] = [
                        strtotime($dt->toDateTimeString()) * 1000,
                        intval($row->pcount)
                    ];
                }

                foreach($results['stats']['population'] as $key => $row)
                    $poptmp[$key] = $row[0];

                array_multisort($poptmp, SORT_ASC, $results['stats']['population']);
            }
            catch(Exception $e)
            {
                $results['stats']['population'] = [];
            }

            $roundstats = DB::table('tbl_extendedroundstats')->select(DB::raw("round_id, min(roundstat_time) AS 'RoundStart', max(roundstat_time) AS 'RoundEnd'"))
                            ->where('server_id', $server->ServerID)
                            ->whereRaw('roundstat_time >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 WEEK)')
                            ->groupBy('round_id')->orderBy('RoundStart', 'desc')->get();

            /*-----  End of Population Chart  ------*/

            if(Auth::check())
            {
                $_tz = Auth::user()->preferences->timezone;
            }
            else $_tz = 'UTC';

            return View::make('public.serverstats')->with('title', Lang::get('navigation.public.statistics'))->with('results', $results)
                        ->with('serverlisting', $serverlisting)
                        ->with('roundstats', $roundstats)
                        ->with('_tz', $_tz);
        }

        return View::make('public.serverstats')->with('title', Lang::get('navigation.public.statistics'))->with('serverlisting', $serverlisting);
    }

    public function showLeaderboardReputation()
    {
        if(Config::get('webadmin.BF3'))
        {
            $bf3Top100 = Reputation::select('target_rep', 'source_rep', 'total_rep', 'total_rep_co', 'SoldierName', 'PlayerID', 'tbl_games.Name')
                        ->join('tbl_playerdata', 'adkats_player_reputation.player_id', '=', 'tbl_playerdata.PlayerID')
                        ->leftJoin('tbl_games', 'tbl_playerdata.GameID', '=', 'tbl_games.GameID')
                        ->where('tbl_games.Name', 'BF3')->orderBy('total_rep_co', 'desc')->take(100)->get();
        }

        if(Config::get('webadmin.BF4'))
        {
            $bf4Top100 = Reputation::select('target_rep', 'source_rep', 'total_rep', 'total_rep_co', 'SoldierName', 'PlayerID', 'tbl_games.Name')
                        ->join('tbl_playerdata', 'adkats_player_reputation.player_id', '=', 'tbl_playerdata.PlayerID')
                        ->leftJoin('tbl_games', 'tbl_playerdata.GameID', '=', 'tbl_games.GameID')
                        ->where('tbl_games.Name', 'BF4')->orderBy('total_rep_co', 'desc')->take(100)->get();
        }

        return View::make('public.leaderboard.reputation')->with('title', 'Reputation Leaderboard')
                ->with('_bf3top100', isset($bf3Top100) ? $bf3Top100 : NULL)
                ->with('_bf4top100', isset($bf4Top100) ? $bf4Top100 : NULL);
    }

    public function showLeaderboardPlayers()
    {
        if(Config::get('webadmin.BF3'))
            $bf3stats = DB::select(File::get(storage_path() . '/sql/top_50_players.sql'), array('game' => Helper::getGameId('BF3')));

        if(Config::get('webadmin.BF4'))
            $bf4stats = DB::select(File::get(storage_path() . '/sql/top_50_players.sql'), array('game' => Helper::getGameId('BF4')));

        return View::make('public.leaderboard.stats')->with('title', 'Stats Leaderboard')
                ->with('_bf3stats', isset($bf3stats) ? $bf3stats : NULL)
                ->with('_bf4stats', isset($bf4stats) ? $bf4stats : NULL);
    }

    public function showMemberlist()
    {
        $users = User::join('bfadmincp_user_preferences', 'bfadmincp_users.id', '=', 'bfadmincp_user_preferences.user_id')
                    ->join('bfadmincp_assigned_roles', 'bfadmincp_users.id', '=', 'bfadmincp_assigned_roles.user_id')
                    ->join('bfadmincp_roles', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_roles.id')
                    ->select('bfadmincp_users.*', 'bfadmincp_user_preferences.gravatar', 'bfadmincp_roles.name AS groupname')->orderBy('username')->paginate(50);

        return View::make('public.memberlist')->with('users', $users)->with('title', 'Memberlist');
    }

    public function rssBans($game)
    {
        $game = strtoupper($game);

        if(!in_array($game, ['BF3', 'BF4']))
            return Helper::response('error', 'Invalid Game Code');

        $gameID = Helper::getGameId($game);

        if($gameID == FALSE)
            return Helper::response('error', 'Invalid Game Code');

        $feed = Feed::make();

        $feed->setCache(0);

        if(!$feed->isCached())
        {
            $feed->title = "Recent " . $game . " Bans";
            $feed->description = "Shows the recent 100 bans from " . $game;
            $feed->link = action('ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex');
            $feed->setDateFormat('datetime');
            $feed->pubdate = Carbon::now();
            $feed->lang = "en";
            $feed->setShortening(true);
            $feed->setTextLimit(300);

            $bans = Ban::select('ban_id', 'player_id', 'target_name', 'source_name', 'source_id', 'ban_status', 'ban_startTime', 'ban_endTime', 'record_message', 'target_id', 'command_action', 'ServerName')
                        ->join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                        ->join('tbl_playerdata', 'adkats_records_main.target_id', '=', 'tbl_playerdata.PlayerID')
                        ->join('tbl_server', 'adkats_records_main.server_id', '=', 'tbl_server.ServerID')
                        ->where('tbl_playerdata.GameID', $gameID)
                        ->where('ban_status', 'Active')
                        ->orderBy('record_time', 'desc')->take(100)->get();

            foreach($bans as $ban)
            {
                $title = "";

                if($ban->command_action == 8) {
                    $title .= "[PERMA BAN] ";
                } else if($ban->command_action == 7) {
                    $title .= "[TEMP BAN] ";
                }

                $title .= $ban->target_name . " for " . $ban->record_message;

                $link = action("ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo", [$ban->target_id, $ban->target_name]);

                $description = sprintf("%s was banned from %s for %s by %s", $ban->target_name, $ban->ServerName, $ban->record_message, $ban->source_name);

                $feed->add(
                    $title,
                    $ban->source_name,
                    $link,
                    $ban->ban_startTime->toISO8601String(),
                    $description
                );
            }
        }

        return $feed->render('atom');
    }
}
