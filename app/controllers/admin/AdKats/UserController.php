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
use ADKGamers\Webadmin\Models\AdKats\Role AS AdKatsRole;
use ADKGamers\Webadmin\Models\AdKats\Soldier AS AdKatsSoldier;
use ADKGamers\Webadmin\Models\AdKats\User AS AdKatsUser;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
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
        View::share('title', 'AdKats Users');

        $tz = Confide::user()->preferences->timezone;
        $users = AdKatsUser::join('adkats_roles', 'adkats_users.user_role', '=', 'adkats_roles.role_id')->orderBy('role_name')->orderBy('user_name')->get();

        $this->layout->content = View::make('admin.adkats.users.userlist')
            ->with('userlist', $users)->with('user_tz', $tz);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        View::share('title', 'Add User');

        $roles = AdKatsRole::orderBy('role_id')->get();

        foreach($roles as $role)
            $rolelist[$role->role_id] = $role->role_name;

        $expireDate = Carbon::parse('+20 years');

        $tz = Confide::user()->preferences->timezone;

        $this->layout->content = View::make('admin.adkats.users.createuser')->with('rolelist', $rolelist)->with('expireDate', $expireDate)->with('user_tz', $tz);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = [
            'username' => 'required|alpha_dash|unique:adkats_users,user_name',
            'email' => 'email|unique:adkats_users,user_email',
            'role' => 'required|exists:adkats_roles,role_id'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@create')->withErrors($validator)->withInput();
        }

        if(Input::has('expireDate') && Input::has('expireTime'))
        {
            $dt = Input::get('expireDate') . ' ' . Input::get('expireTime');
            $dt = Helper::LocalToUTC($dt);
        }
        else
        {
            $dt = Carbon::createFromTimeStampUTC(Input::get('expireDefault'));
        }

        $user = new AdKatsUser;

        $user->user_email      = (Input::has('email') ? trim(Input::get('email')) : NULL);
        $user->user_expiration = $dt->toDateTimeString();
        $user->user_name       = trim(Input::get('username'));
        $user->user_notes      = (Input::has('notes') ? trim(Input::get('notes')) : 'No Notes');
        $user->user_role       = Input::get('role');
        $user->save();

        $successMessage = $user->user_name . ' has been added to the users table.';

        if(Input::has('soldiers'))
        {
            $errors = [];

            $soldiers = array_map('trim', explode(',', Input::get('soldiers')));;
            foreach($soldiers as $key => $soldier)
            {
                if(!is_numeric($soldier))
                {
                    unset($soldiers[$key]);
                    continue;
                }

                $soldierValidator = Validator::make(
                    [ 'player_id' => $soldier],
                    [ 'player_id' => 'unique:adkats_usersoldiers,player_id']
                );

                if($soldierValidator->fails())
                {
                    $messages = $soldierValidator->messages();

                    $player = Player::find($soldier);

                    $takenBy = AdKatsSoldier::where('player_id', $player->PlayerID)->first();

                    $errors[] = sprintf("[%d] %s has already been taken by [%d] %s", $player->PlayerID, $player->SoldierName, $takenBy->user->user_id, $takenBy->user->user_name);

                    continue;
                }

                $userSoldier = new AdKatsSoldier;
                $userSoldier->user_id = $user->user_id;
                $userSoldier->player_id = $soldier;
                $userSoldier->save();
            }

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', [$user->user_id])->withErrors($errors)->with('message', $successMessage);
        }

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', [$user->user_id])->with('message', $successMessage);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = AdKatsUser::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        View::share('title', $user->user_name . ' AdKats Profile');

        $tz = Confide::user()->preferences->timezone;

        $this->layout->content = View::make('admin.adkats.users.showuser')->with('user', $user)->with('user_tz', $tz);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = AdKatsUser::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $roles = AdKatsRole::orderBy('role_id')->get();

        foreach($roles as $role)
            $rolelist[$role->role_id] = $role->role_name;

        foreach($user->soldiers as $soldier)
            $soldiers[] = $soldier->player_id;

        View::share('title', 'Editing User ' . $user->user_name);

        $tz = Confide::user()->preferences->timezone;

        if(!isset($soldiers))
        {
            $this->layout->content = View::make('admin.adkats.users.edit')->with('user', $user)->with('user_tz', $tz)
                                            ->with('rolelist', $rolelist)->with('soldiers', '');
        }
        else
        {
            $this->layout->content = View::make('admin.adkats.users.edit')->with('user', $user)->with('user_tz', $tz)
                                            ->with('rolelist', $rolelist)->with('soldiers', implode(', ', $soldiers));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $user = AdKatsUser::find($id);

        if(!$user)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No user exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $rules = [
            'user_name' => 'required|alpha_dash|unique:adkats_users,user_name,' . $user->user_id . ',user_id',
            'user_email' => 'email|unique:adkats_users,user_email,' . $user->user_id . ',user_id',
            'user_role' => 'required|numeric|exists:adkats_roles,role_id'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@edit')->withErrors($validator)->withInput();
        }

        $username = trim(Input::get('user_name'));
        $email    = trim(Input::get('user_email'));
        $role     = trim(Input::get('user_role'));
        $notes    = trim(Input::get('user_notes'));
        $soldiers = array_map( 'intval', array_map( 'trim', explode( ',' , Input::get('soldiers') ) ) );

        if($username != $user->user_name && !empty($username))
        {
            $user->user_name = $username;
        }

        if( ( is_null($user->user_email) && $email !== '' ) || ( !is_null($user->user_email) && $email != $user->user_email ) )
        {
            if($email === '') $email = NULL;
            $user->user_email = $email;
        }
        else if( !is_null($user->user_email) && $email === '' )
        {
            $user->user_email = NULL;
        }

        if($notes === '')
        {
            $user->user_notes = 'No Notes';
        }
        else
        {
            $user->user_notes = $notes;
        }

        if($role != $user->user_role)
        {
            $user->user_role = $role;
        }

        AdKatsSoldier::destroy($user->user_id);

        foreach($soldiers as $soldier)
        {
            $newSoldier = new AdKatsSoldier;
            $newSoldier->user_id = $user->user_id;
            $newSoldier->player_id = $soldier;
            $newSoldier->save();
        }

        $user->save();

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@edit', [$id])->withInput()->with('message', $user->user_name . ' has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = AdKatsUser::find($id);

        $name = $user->user_name;

        $user->delete();

        $msg = sprintf("%s has been deleted.", $name);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@index')->with('message', $msg);
    }
}
