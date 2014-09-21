<?php namespace ADKGamers\Webadmin\Controllers;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main as Helper;
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommands;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Confide, Preference;

class AccountController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Current user information
     * @var object
     */
    protected $user;

    /**
     * Show the signup page
     */
    public function showAccountSettings()
    {
        View::share('title', 'Account Settings');

        $sound_files = array(
            'alert00' => 'Default',
            'alert01' => 'Skype Ringtone',
            'alert02' => 'Minion Fireman',
            'alert03' => 'Navi, Hey Listen',
            'alert04' => 'Old Spice Whistle',
            'alert05' => 'Warning Alarm',
            'alert06' => 'Pikachu',
            'alert07' => 'Army Wake Up (6 secs)',
            'alert09' => 'Ice Cream Truck Song',
            'alert11' => 'Car Alarm (20 secs)',
            'alert12' => 'Alert Rapid Beep',
            'alert13' => 'Surprise Motherfucker',
            'alert14' => 'Obliteration Bomb Charge',
            'alert15' => 'Beep Beep (Roadrunner)',
        );

        $this->layout->content = View::make('public.user.account.settings')->with('user', Confide::user())->with('sound_files', $sound_files);
    }

    public function showUserProfile($id)
    {
        $user = User::find($id);

        $datetime = Carbon::parse("-3 months");

        $bf3_player = Player::find($user->preferences->bf3_playerid);
        $bf4_player = Player::find($user->preferences->bf4_playerid);

        if($bf3_player)
        {
            $bf3_timeline = Record::where('source_id', $bf3_player->PlayerID)->where('record_time', '>=', $datetime)->whereNotNull('target_id')->get()->toArray();
        } else $bf3_timeline = [];

        if($bf4_player)
        {
            $bf4_timeline = Record::where('source_id', $bf4_player->PlayerID)->where('record_time', '>=', $datetime)->whereNotNull('target_id')->get()->toArray();
        } else $bf4_timeline = [];

        $combinedTimeline = array_merge($bf3_timeline, $bf4_timeline);

        $timeline = [];

        foreach($combinedTimeline as $ct)
        {
            $datekey = date('Ymd', strtotime($ct['record_time']));

            $timeline[$datekey]['date'] = $ct['record_time'];
            $timeline[$datekey]['data'][] = $ct;
        }

        if(!empty($timeline))
        {
            foreach($timeline as $key => $val)
                $orderTimeline[$key] = strtotime($val['date']);

            array_multisort($orderTimeline, SORT_DESC, $timeline);
        }

        $pageTitle = ( is_null($id) ? "My" : $user->username . "'s");

        $pageTitle = sprintf("%s Profile", $pageTitle);

        $servers = [];
        $commands = [];

        foreach(Server::all() as $server)
        {
            $servers[$server->ServerID] = $server->ServerName;
        }

        foreach(AdKatsCommands::all() as $command)
        {
            $commands[$command->command_id] = $command->command_name;
        }

        return View::make('public.user.profile')
                    ->with('timeline', $timeline)
                    ->with('user', $user)
                    ->with('title', $pageTitle)
                    ->with('_pids', ['bf3' => $bf3_player, 'bf4' => $bf4_player])
                    ->with('servers', $servers)
                    ->with('commands', $commands);
    }

    public function updateSettings()
    {
        $user = Confide::user();
        $preferences = $user->preferences;

        $game_id_bf3 = Helper::getGameId('BF3');
        $game_id_bf4 = Helper::getGameId('BF4');

        $rules = array(
            'bf3_player_id' => 'numeric|unique:bfadmincp_user_preferences,bf3_playerid,' . $user->id . ',user_id|exists:tbl_playerdata,PlayerID,GameID,' . $game_id_bf3,
            'bf4_player_id' => 'numeric|unique:bfadmincp_user_preferences,bf4_playerid,' . $user->id . ',user_id|exists:tbl_playerdata,PlayerID,GameID,' . $game_id_bf4,
            'timezone'      => 'timezone',
            'gravatar'      => 'email|unique:bfadmincp_user_preferences,gravatar,' . $user->id . ',user_id',
            'email'         => 'email|unique:bfadmincp_users,email,' . $user->id . ',id',
            'lang'          => 'alpha|in:en,de',
            'password'      => 'min:6|confirmed',
        );

        $messages = array(
            'exists' => 'The :attribute provided does not exist in our database.'
        );

        $validator = Validator::make(Input::all(), $rules, $messages);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\AccountController@showAccountSettings')->withErrors($validator);
        }

        $input_bf3_player_id         = trim(Input::get('bf3_player_id', NULL));
        $input_bf4_player_id         = trim(Input::get('bf4_player_id', NULL));
        $input_timezone              = trim(Input::get('timezone'));
        $input_gravatar              = trim(Input::get('gravatar'));
        $input_lang                  = trim(Input::get('lang'));
        $input_email                 = trim(Input::get('email'));
        $input_password              = trim(Input::get('password'));
        $input_password_confirmation = trim(Input::get('password_confirmation'));

        if(Input::has('bf3_player_id'))
        {
            if($input_bf3_player_id != $preferences->bf3_playerid)
            {
                $preferences->bf3_playerid = $input_bf3_player_id;
            }
        }

        if(Input::has('bf4_player_id'))
        {
            if($input_bf4_player_id != $preferences->bf4_playerid)
            {
                $preferences->bf4_playerid = $input_bf4_player_id;
            }
        }

        if(Input::has('timezone'))
        {
            if($input_timezone != $preferences->timezone)
            {
                $preferences->timezone = $input_timezone;
            }
        }

        if(Input::has('gravatar'))
        {
            if($input_gravatar != $preferences->gravatar)
            {
                $preferences->gravatar = $input_gravatar;
            }
        }

        if(Input::has('lang'))
        {
            if($input_lang != $preferences->lang)
            {
                $preferences->lang = $input_lang;
            }
        }

        if(Input::has('report_enable'))
        {
            $preferences->report_notify_alert = TRUE;
        }
        else
        {
            $preferences->report_notify_alert = FALSE;
        }

        if(Input::has('report_enable_sound'))
        {
            if(Input::has('report_enable'))
            {
                $preferences->report_notify_sound = TRUE;
            }
            else
            {
                $preferences->report_notify_sound = FALSE;
            }
        }
        else
        {
            $preferences->report_notify_sound = FALSE;
        }

        if(Input::has('report_sound_file'))
        {
            if(Input::get('report_sound_file') != $preferences->report_notify_sound_file)
            {
                $preferences->report_notify_sound_file = Input::get('report_sound_file');
            }
        }

        if(Input::has('email'))
        {
            if($input_email != $user->email)
            {
                $user->email = $input_email;
            }
        }

        if(Input::has('password') && Input::has('password_confirmation'))
        {
            if(!empty($input_password) && !empty($input_password_confirmation))
            {
                if($input_password == $input_password_confirmation)
                {
                    $user->password              = Input::get('password');
                    $user->password_confirmation = Input::get('password_confirmation');
                }
            }
        }

        $user->save();
        $preferences->save();

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\AccountController@showAccountSettings')->with('notice', 'Account Settings Updated!');
    }
}
