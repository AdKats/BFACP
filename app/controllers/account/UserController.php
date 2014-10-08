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
use ADKGamers\Webadmin\Models\Battlefield\Player;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Zizaco\Confide\Facade AS Confide, Preference;

class UserController extends \BaseController
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
    public function showSignUp()
    {
        View::share('title', 'Register');
        return View::make('public.user.signup');
    }

    /**
     * Show the sign in page
     */
    public function showSignIn()
    {
        View::share('title', 'Log In');
        return View::make('public.user.login');
    }

    /**
     * Show the forgot password page
     */
    public function showForgotPassword()
    {
        View::share('title', 'Forgot Password');
        return View::make('public.user.forgot_password');
    }

    /**
     * Shows the change password form with the given token
     */
    public function showResetPassword( $token )
    {
        View::share('title', 'Password Reset');
        return View::make('public.user.reset_password')->with('token', $token);
    }

    /**
     * Stores new account
     */
    public function store()
    {
        $validation = Validator::make(Input::all(), array(
            'username' => 'required|alpha_dash|unique:bfadmincp_users,username',
            'email'    => 'required|email|unique:bfadmincp_users,email',
            'timezone' => 'timezone',
            'lang'     => 'alpha|in:en,de',
            'password' => 'min:6|confirmed',
            'bf3pid'   => 'alpha_dash',
            'bf4pid'   => 'alpha_dash'
        ),
        array(
            'bf3pid.alpha_dash' => 'Player name may only contain letters, numbers, dashes, and underscores',
            'bf4pid.alpha_dash' => 'Player name may only contain letters, numbers, dashes, and underscores'
        ));

        if($validation->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignUp')
                    ->withInput(Input::except('password'), Input::except('password_confirmation'))
                    ->withErrors($validation);
        }

        $repo = App::make('UserRepository');

        $user = $repo->signup(Input::all());

        if($user->id)
        {

            if(Config::get('confide::signup_email'))
            {
                Mail::send(Config::get('confide::email_account_confirmation'), ['user' => $user], function($message) use(&$user)
                {
                    $message
                        ->to($user->email, $user->username)
                        ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                });
            }

            if(Input::has('bf3pid'))
            {
                $preference_data['bf3_playerid'] = Player::where('SoldierName', Input::get('bf3pid'))->where('GameID', Helper::getGameId('BF3'))->pluck('PlayerID');

                $player_validation = Validator::make($preference_data, array(
                    'bf3_playerid' => 'unique:bfadmincp_user_preferences,bf3_playerid,NULL,NULL,bf3_playerid,' . $preference_data['bf3_playerid']
                ));

                if(empty($preference_data['bf3_playerid']))
                {
                    $preference_data['bf3_playerid'] = NULL;
                }

                if($player_validation->fails())
                {
                    $pmessages = $player_validation->messages();

                    Session::flash('signup_player_bf3_error', 'Another user has already claimed this player');

                    if($pmessages->has('bf3_playerid'))
                    {
                        $preference_data['bf3_playerid'] = NULL;
                    }
                }
            }

            if(Input::has('bf4pid'))
            {
                $preference_data['bf4_playerid'] = Player::where('SoldierName', Input::get('bf4pid'))->where('GameID', Helper::getGameId('BF4'))->pluck('PlayerID');

                $player_validation = Validator::make($preference_data, array(
                    'bf4_playerid' => 'unique:bfadmincp_user_preferences,bf4_playerid,NULL,NULL,bf4_playerid,' . $preference_data['bf4_playerid']
                ));

                if(empty($preference_data['bf4_playerid']))
                {
                    $preference_data['bf4_playerid'] = NULL;
                }

                if($player_validation->fails())
                {
                    $pmessages = $player_validation->messages();

                    Session::flash('signup_player_bf4_error', 'Another user has already claimed this player');

                    if($pmessages->has('bf4_playerid'))
                    {
                        $preference_data['bf4_playerid'] = NULL;
                    }
                }
            }

            $preference_data['user_id']  = $user->id;
            $preference_data['timezone'] = Input::get('timezone', 'UTC');
            $preference_data['lang']     = Input::get('lang', 'en');

            $preference = Preference::create($preference_data);

            $user->roles()->attach(9);

            Event::fire('admin.user.create', [$user]);
            Event::fire('user.lastseen', [$user]);

            $notice_msg = Lang::get('confide::confide.alerts.account_created') . ' ' . Lang::get('confide::confide.alerts.instructions_sent');

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn')->with('notice', $notice_msg);
        }
        else
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignUp')
                    ->withInput(Input::except('password'), Input::except('password_confirmation'))
                    ->withErrors(array('signup_failed' => 'Unable to register to your account. Please try again.'));
        }
    }

    /**
     * Attempt to send change password link to the given email
     */
    public function do_forgot_password()
    {
        if( Confide::forgotPassword( Input::get( 'email' ) ) )
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showForgotPassword')
                    ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showForgotPassword')
                    ->withErrors(array('forgot_pass_failed' => $error_msg));
        }
    }

    /**
     * Attempt change password of the user
     */
    public function do_reset_password()
    {
        $input = array(
            'token'                 => Input::get( 'token' ),
            'password'              => Input::get( 'password' ),
            'password_confirmation' => Input::get( 'password_confirmation' )
        );

        $repo = App::make('UserRepository');

        if($repo->resetPassword($input))
        {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn')
                            ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showResetPassword', array( 'token' => $input['token']))
                    ->withInput()
                    ->withErrors( $error_msg );
        }
    }

    /**
     * Attempt to do login
     *
     */
    public function do_login()
    {
        $input = array(
            'email'    => Input::get( 'identity' ), // May be the username too
            'username' => Input::get( 'identity' ), // so we have to pass both
            'password' => Input::get( 'password' ),
            'remember' => Input::get( 'remember_me' ),
        );

        $repo = App::make('UserRepository');

        if($repo->login($input))
        {
            return Redirect::intended('/');
        }
        else
        {
            if ($repo->isThrottled($input))
            {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            }
            elseif ($repo->existsButNotConfirmed($input))
            {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            }
            else
            {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn')
                    ->withInput(Input::except('password'))
                    ->withErrors($err_msg);
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    public function confirm($code)
    {
        if(Confide::confirm($code))
        {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn')
                    ->with( 'notice', $notice_msg );
        }
        else
        {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn')
                    ->withErrors( $error_msg );
        }
    }

    /**
     * Log the user out of the application.
     *
     */
    public function logout()
    {
        User::$rules = array();
        Confide::logout();

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex');
    }
}
