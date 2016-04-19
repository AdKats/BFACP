<?php namespace BFACP\Http\Controllers;

use BFACP\Account\User;
use BFACP\Repositories\UserRepository;
use Former\Facades\Former;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UsersController extends BaseController
{
    /**
     * User Repository
     *
     * @var UserRepository
     */
    private $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = App::make('BFACP\Repositories\UserRepository');
    }

    /**
     * Show the account settings page
     *
     * @return View
     */
    public function showAccountSettings()
    {
        $page_title = 'Account Settings';
        $user =& $this->user;

        // Populate the form fields with the user information
        Former::populate($user);

        return View::make('user.account-settings', compact('user', 'page_title'));
    }

    /**
     * Save the changes made to the users account
     *
     * @return Redirect
     */
    public function saveAccountSettings()
    {
        $user = \Auth::user();

        $email = trim(Input::get('email', null));
        $lang = trim(Input::get('language', null));
        $password = trim(Input::get('password', null));
        $password_confirmation = trim(Input::get('password_confirmation', null));

        $v = Validator::make(Input::all(), [
            'email'    => 'required|email|unique:bfacp_users,email,' . $user->id,
            'language' => 'required|in:' . implode(',', array_keys(Config::get('bfacp.site.languages'))),
            'password' => 'min:8|confirmed',
        ]);

        if ($v->fails()) {
            return Redirect::route('user.account')->withErrors($v)->withInput();
        }

        // Update email
        if ($email != $user->email) {
            $user->email = $email;
            $this->messages[] = Lang::get('user.notifications.account.email.changed', ['addr' => $email]);
        }

        // Update the user language if it's been changed
        if ($lang != $user->setting->lang) {
            $user->setting()->update([
                'lang' => $lang,
            ]);

            $langHuman = Config::get('bfacp.site.languages')[ $lang ];

            $this->messages[] = Lang::get('user.notifications.account.language.changed', ['lang' => $langHuman]);
        }

        // Change the user password if they filled out the fields and new passwords match
        if (Input::has('password') && Input::has('password_confirmation') && $password == $password_confirmation) {
            $user->password = $password;
            $user->password_confirmation = $password;
            $this->messages[] = Lang::get('user.notifications.password.email.changed');
        }

        $user->save();

        return Redirect::route('user.account')->withMessages($this->messages);
    }

    /**
     * Shows the login form
     *
     * @return View
     */
    public function showLogin()
    {
        return View::make('user.login');
    }

    /**
     * Shows the signup form
     *
     * @return View
     */
    public function showSignup()
    {
        return View::make('user.register');
    }

    /**
     * Attempt to confirm the account with code
     *
     * @param  string $code
     *
     * @return Redirect
     */
    public function confirm($code)
    {
        // If the code is valid then redirect to the login page.
        if ($this->repository->confirm($code)) {
            return Redirect::route('user.login')->with('messages', [
                Lang::get('confide::confide.alerts.confirmation'),
            ]);
        }

        return Redirect::route('user.login')->with('error', Lang::get('confide::confide.alerts.wrong_confirmation'));
    }

    /**
     * Create a new user
     *
     * @return Redirect
     */
    public function signup()
    {
        $input = Input::all();

        $v = Validator::make($input, array_merge(User::$rules, [
            'ign' => 'regex:/^([a-zA-Z0-9_\-]+)$/',
        ]));

        if ($v->fails()) {
            return Redirect::route('user.register')->withInput(Input::except('password',
                'password_confirmation'))->withErrors($v);
        }

        $user = $this->repository->signup($input, 2, false, false, true);

        if (is_null($user->id)) {
            return Redirect::route('user.register')->withInput(Input::except('password',
                'password_confirmation'))->withErrors($user->errors());
        }

        if (Config::get('confide::signup_email')) {
            Mail::queueOn(Config::get('confide::email_queue'), 'emails.user.signup', compact('user'),
                function ($message) use ($user) {
                    $message->to($user->email,
                        $user->username)->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                });
        }

        return Redirect::route('user.login')->with('messages', [
            Lang::get('confide::confide.alerts.account_created'),
            Lang::get('confide::confide.alerts.instructions_sent'),
        ]);
    }

    /**
     * Attempts to login with the given credentials.
     */
    public function login()
    {
        $input = Input::all();

        if ($this->repository->login($input)) {
            return Redirect::intended();
        }

        if ($this->repository->isThrottled($input)) {
            $error = Lang::get('confide::confide.alerts.too_many_attempts');
        } elseif ($this->repository->existsButNotConfirmed($input)) {
            $error = Lang::get('confide::confide.alerts.not_confirmed');
        } else {
            $error = Lang::get('confide::confide.alerts.wrong_credentials');
        }

        return Redirect::route('user.login')->withInput(Input::except('password'))->with('error', $error);
    }

    /**
     * Log out the user
     */
    public function logout()
    {
        $this->repository->logout();

        // If the application requires a login redirect
        // to the login form instead of the dashboard.
        if (Config::get('bfacp.site.auth')) {
            return Redirect::route('user.login');
        }

        return Redirect::route('home');
    }
}
