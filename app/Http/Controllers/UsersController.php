<?php

namespace BFACP\Http\Controllers;

use BFACP\Account\User;
use BFACP\Repositories\UserRepository;
use Former\Facades\Former;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

/**
 * Class UsersController.
 */
class UsersController extends Controller
{
    /**
     * User Repository.
     *
     * @var UserRepository
     */
    private $repository;

    /**
     * Show the account settings page.
     *
     * @return View
     */
    public function showAccountSettings()
    {
        $page_title = 'Account Settings';
        $user = &$this->user;

        // Populate the form fields with the user information
        Former::populate($user);

        return view('user.account-settings', compact('user', 'page_title'));
    }

    /**
     * Save the changes made to the users account.
     *
     * @return Redirect
     */
    public function saveAccountSettings()
    {
        $user = \Auth::user();

        $email = trim($this->request->get('email', null));
        $lang = trim($this->request->get('language', null));
        $password = trim($this->request->get('password', null));
        $password_confirmation = trim($this->request->get('password_confirmation', null));

        $v = Validator::make($this->request->all(), [
            'email'    => 'required|email|unique:bfacp_users,email,'.$user->id,
            'language' => 'required|in:'.implode(',', array_keys($this->config->get('bfacp.site.languages'))),
            'password' => 'min:8|confirmed',
        ]);

        if ($v->fails()) {
            return redirect()->route('user.account')->withErrors($v)->withInput();
        }

        // Update email
        if ($email != $user->email) {
            $user->email = $email;
            $this->messages[] = trans('user.notifications.account.email.changed', ['addr' => $email]);
        }

        // Update the user language if it's been changed
        if ($lang != $user->setting->lang) {
            $user->setting()->update([
                'lang' => $lang,
            ]);

            $langHuman = $this->config->get('bfacp.site.languages')[$lang];

            $this->messages[] = trans('user.notifications.account.language.changed', ['lang' => $langHuman]);
        }

        // Change the user password if they filled out the fields and new passwords match
        if ($this->request->has('password') && $this->request->has('password_confirmation') && $password == $password_confirmation) {
            $user->password = $password;
            $user->password_confirmation = $password;
            $this->messages[] = trans('user.notifications.password.email.changed');
        }

        $user->save();

        return redirect()->route('user.account')->withMessages($this->messages);
    }

    /**
     * Shows the login form.
     *
     * @return View
     */
    public function showLogin()
    {
        return view('user.login');
    }

    /**
     * Shows the signup form.
     *
     * @return View
     */
    public function showSignup()
    {
        return view('user.register');
    }

    /**
     * Attempt to confirm the account with code.
     *
     * @param string $code
     *
     * @return Redirect
     */
    public function confirm($code)
    {
        $this->repository = app(UserRepository::class);

        // If the code is valid then redirect to the login page.
        if ($this->repository->confirm($code)) {
            return redirect()->route('user.login')->with('messages', [
                trans('confide::confide.alerts.confirmation'),
            ]);
        }

        return redirect()->route('user.login')->with('error', trans('confide::confide.alerts.wrong_confirmation'));
    }

    /**
     * Create a new user.
     *
     * @return Redirect
     */
    public function signup()
    {
        $input = $this->request->all();

        $this->repository = app(UserRepository::class);

        $v = Validator::make($input, array_merge(User::$rules, [
            'ign' => 'regex:/^([a-zA-Z0-9_\-]+)$/',
        ]));

        if ($v->fails()) {
            return redirect()->route('user.register')->withInput($this->request->except('password',
                'password_confirmation'))->withErrors($v);
        }

        $user = $this->repository->signup($input, 2, false, true);

        if (is_null($user->id)) {
            return redirect()->route('user.register')->withInput($this->request->except('password',
                'password_confirmation'))->withErrors($user->errors());
        }

        if ($this->config->get('confide::signup_email')) {
            Mail::queueOn($this->config->get('confide::email_queue'), 'emails.user.signup', compact('user'),
                function ($message) use ($user) {
                    $message->to($user->email,
                        $user->username)->subject(trans('confide::confide.email.account_confirmation.subject'));
                });
        }

        return redirect()->route('user.login')->with('messages', [
            trans('confide::confide.alerts.account_created'),
            trans('confide::confide.alerts.instructions_sent'),
        ]);
    }

    /**
     * Attempts to login with the given credentials.
     */
    public function login()
    {
        $input = $this->request->all();

        $this->repository = app(UserRepository::class);

        if ($this->repository->login($input)) {
            return Redirect::intended();
        }

        if ($this->repository->isThrottled($input)) {
            $error = trans('confide::confide.alerts.too_many_attempts');
        } elseif ($this->repository->existsButNotConfirmed($input)) {
            $error = trans('confide::confide.alerts.not_confirmed');
        } else {
            $error = trans('confide::confide.alerts.wrong_credentials');
        }

        return redirect()->route('user.login')->withInput($this->request->except('password'))->with('error', $error);
    }

    /**
     * Log out the user.
     */
    public function logout()
    {
        $this->repository = app(UserRepository::class);

        $this->repository->logout();

        // If the application requires a login redirect
        // to the login form instead of the dashboard.
        if ($this->config->get('bfacp.site.auth')) {
            return redirect()->route('user.login');
        }

        return redirect()->route('home');
    }
}
