<?php namespace ADKGamers\Webadmin\Controllers\Admin;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Libs\UptimeRobotAPI;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Models\Battlefield\Setting AS GSetting;
use ADKGamers\Webadmin\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Role, Preference, Permission, WebadminException, Exception;
use Zizaco\Confide\Facade AS Confide;

class SiteGameServerController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        View::share('title', 'Game Server Management');

        $servers = Server::join('tbl_games', 'tbl_server.GameID', '=', 'tbl_games.GameID')->orderBy('ServerID')->get();

        $this->layout->content = View::make('admin.site_game_server.index')->with('servers', $servers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $server = Server::with('setting')->where('ServerID', $id)->first();

        if(!$server)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No server exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        if(is_null($server->setting))
        {
            $setting = new GSetting;
            $setting = $server->setting()->save($setting);
            return Redirect::refresh();
        }

        View::share('title', 'Edit Game Server');

        $this->layout->content = View::make('admin.site_game_server.edit')->with('server', $server);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $server = Server::find($id);

        if(!$server)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No server exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $setting = GSetting::find($id);

        $rconPassword = trim(Input::get('rcon_password', ''));
        $filter       = trim(Input::get('namestrip', ''));
        $useServer    = Input::get('enable_server');

        if($useServer != $server->ConnectionState)
        {
            $server->ConnectionState = $useServer;
            $server->save();
        }

        if(!empty($rconPassword))
        {
            $rcon_hash = Hash::make($rconPassword);

            $setting->rcon_pass_hash = $rcon_hash;

            $setting->save();
        }

        if(!empty($filter))
        {
            $setting->name_strip = $filter;

            $setting->save();
        }

        if(Input::has('useUptimeRobot') && Input::get('useUptimeRobot') == 1)
        {
            try
            {
                $api = new UptimeRobotAPI;

                $request = $api->newMonitor($server);

                $setting->uptime_robot_id = $request['monitor']['id'];
                $setting->save();
            }
            catch(WebadminException $e)
            {
                return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController@edit', [$id])->with('message', 'Configuration Saved')->withErrors([
                    'Unable to add the server to UptimeRobot. Reason: ' . $e->getMessage()
                ]);
            }
        }

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController@edit', [$id])->with('message', 'Configuration Saved');
    }
}
