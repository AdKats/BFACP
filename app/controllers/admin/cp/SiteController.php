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
use ADKGamers\Webadmin\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Role, Preference, Permission;
use Zizaco\Confide\Facade AS Confide;

class SiteController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        View::share('title', 'Site Settings');

        $settings = [];

        foreach(Setting::all() as $setting)
        {
            if(in_array($setting->token, ['BF3', 'BF4', 'MOTD', 'FORCESSL', 'ONLYAUTHUSERS', 'UPTIMEROBOT']))
            {
                if($setting->context == 1)
                {
                    $context = TRUE;
                } else $context = FALSE;
            }
            else
            {
                $context = $setting->context;
            }


            $settings[$setting->token] = ['value' => $context, 'description' => $setting->description];
        }

        $serverSort = array(
            'ServerID' => 'Server ID',
            'ServerName' => 'Server Name'
        );

        $this->layout->content = View::make('admin.site_settings.index')->with('settings', $settings)->with('serverSort', $serverSort);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $settings = [];

        foreach(Setting::all() as $setting)
        {
            if(in_array($setting->token, ['BF3', 'BF4', 'MOTD', 'FORCESSL', 'ONLYAUTHUSERS', 'UPTIMEROBOT']))
            {
                if($setting->context == 1)
                {
                    $context = TRUE;
                } else $context = FALSE;
            }
            else
            {
                $context = $setting->context;
            }


            $settings[$setting->token] = ['value' => $context, 'description' => $setting->description];
        }

        $input_bf3              = Input::get('BF3', TRUE);
        $input_bf4              = Input::get('BF4', TRUE);
        $input_order            = Input::get('SERVERORDER', 'ServerID');
        $input_metabans_key     = Input::get('MB-KEY', "");
        $input_metabans_user    = Input::get('MB-USR', "");
        $input_metabans_account = Input::get('MB-ACC', "");
        $input_motd             = Input::get('MOTD', TRUE);
        $input_motd_text        = Helper::cleanupHTML(Input::get('MOTD-TXT', ""));
        $input_authonly         = Input::get('ONLYAUTHUSERS', TRUE);
        $input_forcessl         = Input::get('FORCESSL', TRUE);
        $input_uptimerobot      = Input::get('UPTIMEROBOT', TRUE);
        $input_uptimerobot_key  = Input::get('UPTIMEROBOT-KEY', "");

        $messages = [];

        /*================================
        =            Booleans            =
        ================================*/

        if($settings['BF3']['value'] != $input_bf3)
        {
            Setting::where('token', 'BF3')->update(['context' => $input_bf3]);
            $messages[] = "Battlefield 3 has been " . ($input_bf3 ? 'Enabled' : 'Disabled');
        }

        if($settings['BF4']['value'] != $input_bf4)
        {
            Setting::where('token', 'BF4')->update(['context' => $input_bf4]);
            $messages[] = "Battlefield 4 has been " . ($input_bf4 ? 'Enabled' : 'Disabled');
        }

        if($settings['ONLYAUTHUSERS']['value'] != $input_authonly)
        {
            Setting::where('token', 'ONLYAUTHUSERS')->update(['context' => $input_authonly]);
            $messages[] = "Only authorized users has been " . ($input_authonly ? 'Enabled' : 'Disabled');
        }

        if($settings['FORCESSL']['value'] != $input_forcessl)
        {
            Setting::where('token', 'FORCESSL')->update(['context' => $input_forcessl]);
            $messages[] = "Force SSL connection has been " . ($input_forcessl ? 'Enabled' : 'Disabled');
        }

        /*-----  End of Booleans  ------*/


        /*====================================
        =            Uptime Robot            =
        ====================================*/

        if($settings['UPTIMEROBOT']['value'] != $input_uptimerobot)
        {
            Setting::where('token', 'UPTIMEROBOT')->update(['context' => $input_uptimerobot]);
            $messages[] = "Uptime Robot has been " . ($input_uptimerobot ? 'Enabled' : 'Disabled');
        }

        if($settings['UPTIMEROBOT-KEY']['value'] != $input_uptimerobot_key)
        {
            Setting::where('token', 'UPTIMEROBOT-KEY')->update(['context' => $input_uptimerobot_key]);
        }

        /*-----  End of Uptime Robot  ------*/


        /*================================
        =            Metabans            =
        ================================*/

        if($settings['MB-KEY']['value'] != $input_metabans_key)
        {
            Setting::where('token', 'MB-KEY')->update(['context' => $input_metabans_key]);
        }

        if($settings['MB-USR']['value'] != $input_metabans_user)
        {
            Setting::where('token', 'MB-USR')->update(['context' => $input_metabans_user]);
        }

        if($settings['MB-ACC']['value'] != $input_metabans_account)
        {
            Setting::where('token', 'MB-ACC')->update(['context' => $input_metabans_account]);
        }

        /*-----  End of Metabans  ------*/

        /*==========================================
        =            Message of the Day            =
        ==========================================*/

        if($settings['MOTD']['value'] != $input_motd)
        {
            Setting::where('token', 'MOTD')->update(['context' => $input_motd]);
            $messages[] = "Message of the Day has been " . ($input_motd ? 'Enabled' : 'Disabled');
        }

        if($settings['MOTD-TXT']['value'] != $input_motd_text)
        {
            Setting::where('token', 'MOTD-TXT')->update(['context' => $input_motd_text]);
        }

        /*-----  End of Message of the Day  ------*/


        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\SiteController@index')->with('messages', $messages);
    }
}
