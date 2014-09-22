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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Role, Preference, Permission;
use Zizaco\Confide\Facade AS Confide;

class UserController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    	View::share('title', 'Site Users');

    	$tz = Confide::user()->preferences->timezone;

    	$users = User::select('bfadmincp_users.id', 'username', 'email', 'confirmed', 'bfadmincp_users.created_at', 'name')
                    ->join('bfadmincp_assigned_roles', 'bfadmincp_users.id', '=', 'bfadmincp_assigned_roles.user_id')
                    ->join('bfadmincp_roles', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_roles.id')
                    ->orderBy('bfadmincp_roles.name')
                    ->orderBy('username')->get();

    	$this->layout->content = View::make('admin.users.userlist')
                ->with('users', $users)
                ->with('user_tz', $tz);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $roles = Role::all();

        foreach($roles as $role)
            $rolelist[$role->id] = $role->name;

        View::share('title', 'Editing User ' . $user->user_name);

        $tz = Confide::user()->preferences->timezone;

        $bf3player = $user->preferences->bf3player;
        $bf4player = $user->preferences->bf4player;

        $soldiers = [];

        if(!empty($bf3player))
        {
            $soldiers[] = [
                'gameIdent' => $bf3player->gameIdent(),
                'soldier' => $bf3player
            ];
        }

        if(!empty($bf4player))
        {
            $soldiers[] = [
                'gameIdent' => $bf4player->gameIdent(),
                'soldier' => $bf4player
            ];
        }


        $this->layout->content = View::make('admin.users.edit')
                    ->with('user', $user)
                    ->with('soldiers', $soldiers)
                    ->with('user_tz', $tz)
                    ->with('rolelist', $rolelist);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $preferences = $user->preferences;

        $game_id_bf3 = Helper::getGameId('BF3');
        $game_id_bf4 = Helper::getGameId('BF4');

        $rules = array(
            'bf3_player_id' => 'numeric|unique:bfadmincp_user_preferences,bf3_playerid,' . $user->id . ',user_id|exists:tbl_playerdata,PlayerID,GameID,' . $game_id_bf3,
            'bf4_player_id' => 'numeric|unique:bfadmincp_user_preferences,bf4_playerid,' . $user->id . ',user_id|exists:tbl_playerdata,PlayerID,GameID,' . $game_id_bf4,
            'timezone'      => 'timezone',
            'email'         => 'email|unique:bfadmincp_users,email,' . $user->id . ',id',
            'lang'          => 'alpha|in:en,de',
            'password'      => 'min:6|confirmed',
            'username'      => 'alpha_dash|unique:bfadmincp_users,username,' . $user->id . ',id'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@edit', [$id])->withErrors($validator);
        }

        $input_username         = trim(Input::get('username'));
        $input_email            = trim(Input::get('email'));
        $input_password         = trim(Input::get('password'));
        $input_password_confirm = trim(Input::get('password_confirmation'));
        $input_lang             = Input::get('lang');
        $input_tz               = Input::get('timezone');
        $input_role             = Input::get('role', 9);
        $input_bf3_player       = trim(Input::get('bf3_player_id'));
        $input_bf4_player       = trim(Input::get('bf4_player_id'));
        $input_member_status    = Input::get('account_status', FALSE);

        if(Input::has('account_status'))
        {
            if($input_member_status != $user->confirmed)
            {
                $user->confirmed = $input_member_status;
            }
        }

        if(Input::has('bf3_player_id'))
        {
            if($input_bf3_player != $preferences->bf3_playerid)
            {
                $preferences->bf3_playerid = $input_bf3_player;
            }
        }

        if(Input::has('bf4_player_id'))
        {
            if($input_bf4_player != $preferences->bf4_playerid)
            {
                $preferences->bf4_playerid = $input_bf4_player;
            }
        }

        if(Input::has('timezone'))
        {
            if($input_tz != $preferences->timezone)
            {
                $preferences->timezone = $input_tz;
            }
        }

        if(Input::has('lang'))
        {
            if($input_lang != $preferences->lang)
            {
                $preferences->lang = $input_lang;
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
            if($input_password == $input_password_confirm && !empty($input_password))
            {
                $user->password              = $input_password;
                $user->password_confirmation = $input_password_confirm;

                \Mail::send('emails.user.passwordchange', ['user' => $user, 'newpassword' => $input_password], function($message) use (&$user)
                {
                    $message->to($user->email, $user->username)->subject("Password has been changed");
                });
            }
        }

        if($input_role != $user->roleId())
        {
            $user->roles()->detach($user->roleId());
            $user->roles()->attach($input_role);
        }

        $user->save();
        $preferences->save();

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@edit', [$id])->with('message', sprintf("%s account has been updated", $user->username));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $name = $user->username;

        $user->roles()->detach($user->roleId());
        $user->updateUniques();

        $user->delete();

        $msg = sprintf("%s has been deleted.", $name);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@index')->with('message', $msg);
    }
}
