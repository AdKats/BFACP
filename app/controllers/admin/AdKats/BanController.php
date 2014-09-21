<?php namespace ADKGamers\Webadmin\Controllers\Admin\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Zizaco\Confide\Facade AS Confide;
use Zizaco\Entrust\EntrustFacade AS Entrust;

class BanController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $bans = Ban::join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                    ->join('tbl_server', 'adkats_records_main.server_id', '=', 'tbl_server.ServerID')
                    ->join('tbl_games', 'tbl_server.GameID', '=', 'tbl_games.GameID')
                    ->orderBy('ban_startTime', 'desc')->paginate(200);

        View::share('title', 'Ban Listing');

        $this->layout->content = View::make('admin.adkats.bans.banlist')->with('bans', $bans);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $id = Input::get('id', NULL);

        if(!Entrust::can('issuetban') && !Entrust::can('issuepban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@index', [])->withErrors(['You do not have permission to issue bans']);
        }

        if(is_null($id) || !is_numeric($id))
        {
            return Redirect::back();
        }

        $player = Player::find($id);

        if(!$player)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No player exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        if($player->recentBanExist())
        {
            $ban = $player->recentBan;
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$ban->ban_id]);
        }

        $game = $player->gameIdent();

        if($game == 'BF3')
        {
            foreach(Server::bf3()->get() as $server)
            {
                $_servers[$server->ServerID] = $server->ServerName;
            }
        }
        elseif($game == 'BF4')
        {
            foreach(Server::bf4()->get() as $server)
            {
                $_servers[$server->ServerID] = $server->ServerName;
            }
        }

        View::share('title', 'Create New Ban');

        $this->layout->content = View::make('admin.adkats.bans.createban')->with('player', $player)->with('_gameName', $game)->with('_servers', $_servers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('target_id'))
        {
            return Redirect::back()->withErrors(['No player id was set']);
        }

        if(!Entrust::can('issuetban') && !Entrust::can('issuepban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@index', [])->withErrors(['You do not have permission to issue bans']);
        }

        $redirect_url = action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@create') . "?" . http_build_query(['id' => Input::get('target_id')]);

        $preferences = Auth::user()->preferences;

        $tz = $preferences->timezone;

        switch(Input::get('_gameName'))
        {
            case "BF3":
                if(is_null($preferences->bf3_playerid))
                {
                    $source_player_name = Auth::user()->username;
                    $source_player_id = NULL;
                }
                else
                {
                    $sourcePlayerQuery = Player::where('GameID', Input::get('_gameID'))->where('PlayerID', $preferences->bf3_playerid)->first();
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
                    $sourcePlayerQuery = Player::where('GameID', Input::get('_gameID'))->where('PlayerID', $preferences->bf4_playerid)->first();
                    $source_player_name = $sourcePlayerQuery->SoldierName;
                    $source_player_id = $sourcePlayerQuery->PlayerID;
                }
            break;

            default:
                $source_player_name = Auth::user()->username;
                $source_player_id = NULL;
            break;
        }

        $ban_player     = trim(Input::get('target_name'));
        $ban_player_id  = trim(Input::get('target_id'));
        $ban_reason     = trim(Input::get('ban_reason'));
        $ban_notes      = trim(Input::get('ban_notes'));
        $ban_server     = Input::get('ban_server');
        $ban_status     = Input::get('ban_status');
        $ban_type       = Input::get('ban_type');
        $ban_start_date = trim(Input::get('ban_start_date'));
        $ban_end_date   = trim(Input::get('ban_end_date'));
        $ban_start_time = trim(Input::get('ban_start_time'));
        $ban_end_time   = trim(Input::get('ban_end_time'));

        $ban_start_date_time = Carbon::createFromFormat('m/d/Y g:i A', sprintf("%s %s", $ban_start_date, $ban_start_time), $tz)->toDateTimeString();
        $ban_end_date_time   = Carbon::createFromFormat('m/d/Y g:i A', sprintf("%s %s", $ban_end_date, $ban_end_time), $tz)->toDateTimeString();

        if($ban_type == 8)
        {
            $ban_start_convert = Carbon::now();
            $ban_end_convert = Carbon::now()->addYears(20);
        }
        else
        {
            $ban_start_convert = Helper::LocalToUTC($ban_start_date_time);
            $ban_end_convert   = Helper::LocalToUTC($ban_end_date_time);
        }

        if($ban_end_convert->lte($ban_start_convert))
            return Redirect::to($redirect_url)->withErrors(['Ban end date/time can\'t be before the start date/time.'])->withInput();

        if($ban_type == 7 && !Entrust::can('issuetban'))
        {
            return Redirect::to($redirect_url)->withErrors(['You do not have permission to temp ban the player'])->withInput();
        }

        if($ban_type == 8 && !Entrust::can('issuepban'))
        {
            return Redirect::to($redirect_url)->withErrors(['You do not have permission to perma ban the player'])->withInput();
        }

        $record = new Record;
        $record->server_id       = $ban_server;
        $record->command_type    = $ban_type;
        $record->command_action  = $ban_type;
        $record->command_numeric = $ban_start_convert->diffInMinutes($ban_end_convert);
        $record->target_name     = $ban_player;
        $record->target_id       = $ban_player_id;
        $record->source_name     = $source_player_name;
        $record->source_id       = $source_player_id;
        $record->record_message  = $ban_reason;
        $record->record_time     = $ban_start_convert->toDateTimeString();
        $record->adkats_read     = 'Y';
        $record->adkats_web      = TRUE;
        $record->save();

        $ban = new Ban;
        $ban->player_id        = $ban_player_id;
        $ban->latest_record_id = $record->record_id;
        $ban->ban_notes        = $ban_notes;
        $ban->ban_status       = $ban_status;
        $ban->ban_startTime    = $ban_start_convert->toDateTimeString();
        $ban->ban_endTime      = $ban_end_convert->toDateTimeString();
        $ban->ban_enforceName  = Input::get('ban_enforceName');
        $ban->ban_enforceGUID  = Input::get('ban_enforceGUID');
        $ban->ban_enforceIP    = Input::get('ban_enforceIP');
        $ban->save();

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$ban->ban_id])->with('message', sprintf("Ban #%u has been created.", $ban->ban_id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if(!Entrust::can('issuetban') && !Entrust::can('issuepban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@index', [])->withErrors(['You do not have permission to issue bans']);
        }

        $ban = Ban::join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                    ->join('tbl_server', 'adkats_records_main.server_id', '=', 'tbl_server.ServerID')
                    ->join('tbl_games', 'tbl_server.GameID', '=', 'tbl_games.GameID')
                    ->where('ban_id', $id)->first();

        if(!$ban)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No ban exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        if($ban->Name == 'BF3')
        {
            foreach(Server::bf3()->get() as $server)
            {
                $_servers[$server->ServerID] = $server->ServerName;
            }
        }
        elseif($ban->Name == 'BF4')
        {
            foreach(Server::bf4()->get() as $server)
            {
                $_servers[$server->ServerID] = $server->ServerName;
            }
        }

        $title = sprintf("Editing Ban #%u", $ban->ban_id);

        View::share('title', $title);

        $this->layout->content = View::make('admin.adkats.bans.edit')->with('ban', $ban)->with('_servers', $_servers);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if(!Entrust::can('issuetban') && !Entrust::can('issuepban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@index', [])->withErrors(['You do not have permission to issue bans']);
        }

        $ban = Ban::find($id);

        if(!$ban)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No ban exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $record = Record::find($ban->latest_record_id);

        $preferences = Auth::user()->preferences;

        $tz = $preferences->timezone;

        switch(Input::get('_gameName'))
        {
            case "BF3":
                if(is_null($preferences->bf3_playerid))
                {
                    $source_player_name = Auth::user()->username;
                    $source_player_id = NULL;
                }
                else
                {
                    $sourcePlayerQuery = Player::where('GameID', Input::get('_gameID'))->where('PlayerID', $preferences->bf3_playerid)->first();
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
                    $sourcePlayerQuery = Player::where('GameID', Input::get('_gameID'))->where('PlayerID', $preferences->bf4_playerid)->first();
                    $source_player_name = $sourcePlayerQuery->SoldierName;
                    $source_player_id = $sourcePlayerQuery->PlayerID;
                }
            break;

            default:
                $source_player_name = Auth::user()->username;
                $source_player_id = NULL;
            break;
        }

        $ban_reason     = trim(Input::get('ban_reason'));
        $ban_notes      = trim(Input::get('ban_notes'));
        $ban_server     = Input::get('ban_server');
        $ban_status     = Input::get('ban_status');
        $ban_type       = Input::get('ban_type');
        $ban_start_date = trim(Input::get('ban_start_date'));
        $ban_end_date   = trim(Input::get('ban_end_date'));
        $ban_start_time = trim(Input::get('ban_start_time'));
        $ban_end_time   = trim(Input::get('ban_end_time'));

        $ban_start_date_time = Carbon::createFromFormat('m/d/Y g:i A', sprintf("%s %s", $ban_start_date, $ban_start_time), $tz)->toDateTimeString();
        $ban_end_date_time   = Carbon::createFromFormat('m/d/Y g:i A', sprintf("%s %s", $ban_end_date, $ban_end_time), $tz)->toDateTimeString();

        if($ban_type == 8)
        {
            $ban_start_convert = Carbon::now();
            $ban_end_convert = Carbon::now()->addYears(20);
        }
        else
        {
            $ban_start_convert = Helper::LocalToUTC($ban_start_date_time);
            $ban_end_convert   = Helper::LocalToUTC($ban_end_date_time);
        }

        if($ban_end_convert->lte($ban_start_convert))
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$id])->withErrors(['Ban end date/time can\'t be before the start date/time.']);

        if($ban_type == 7 && !Entrust::can('issuetban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$id])->withErrors(['You do not have permission to temp ban the player'])->withInput();
        }

        if($ban_type == 8 && !Entrust::can('issuepban'))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$id])->withErrors(['You do not have permission to perma ban the player'])->withInput();
        }

        if($source_player_name != $record->source_name || $ban_reason != $record->record_message || $ban_type != $record->command_action)
        {
            $newRecord = new Record;
            $newRecord->server_id       = $ban_server;
            $newRecord->command_type    = $ban_type;
            $newRecord->command_action  = $ban_type;
            $newRecord->command_numeric = $ban_start_convert->diffInMinutes($ban_end_convert);
            $newRecord->target_name     = $record->target_name;
            $newRecord->target_id       = $record->target_id;
            $newRecord->source_name     = $source_player_name;
            $newRecord->source_id       = $source_player_id;
            $newRecord->record_message  = $ban_reason;
            $newRecord->record_time     = ($ban_start_convert->toDateTimeString() == $record->record_time ? $ban_start_convert->addSecond()->toDateTimeString() : $ban_start_convert->toDateTimeString());
            $newRecord->adkats_read     = 'Y';
            $newRecord->adkats_web      = TRUE;
            $newRecord->save();

            $record->command_action = ($ban_type == 8 ? 73 : 72);
            $record->save();

            $ban->latest_record_id = $newRecord->record_id;
            $ban->ban_notes        = $ban_notes;
            $ban->ban_status       = $ban_status;
            $ban->ban_startTime    = ($ban_start_convert->toDateTimeString() == $record->record_time ? $ban_start_convert->addSecond()->toDateTimeString() : $ban_start_convert->toDateTimeString());
            $ban->ban_endTime      = $ban_end_convert->toDateTimeString();
            $ban->ban_enforceName  = Input::get('ban_enforceName');
            $ban->ban_enforceGUID  = Input::get('ban_enforceGUID');
            $ban->ban_enforceIP    = Input::get('ban_enforceIP');
            $ban->save();
        }
        else
        {
            if($ban_notes != $ban->ban_notes)
            {
                $ban->ban_notes = $ban_notes;
            }

            if($ban_status != $ban->ban_status)
            {
                $ban->ban_status = $ban_status;
            }

            if(Input::get('ban_enforceName') != $ban->ban_enforceName)
            {
                $ban->ban_enforceName = Input::get('ban_enforceName');
            }

            if(Input::get('ban_enforceGUID') != $ban->ban_enforceGUID)
            {
                $ban->ban_enforceGUID = Input::get('ban_enforceGUID');
            }

            if(Input::get('ban_enforceIP') != $ban->ban_enforceIP)
            {
                $ban->ban_enforceIP = Input::get('ban_enforceIP');
            }

            if($ban_type != 8)
            {
                $ban->ban_startTime = ($ban_start_convert->toDateTimeString() == $record->record_time ? $ban_start_convert->addSecond()->toDateTimeString() : $ban_start_convert->toDateTimeString());
                $ban->ban_endTime   = $ban_end_convert->toDateTimeString();
            }

            $ban->save();
        }

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@edit', [$id])->with('message', sprintf("Ban #%u has been updated.", $id));
    }
}
