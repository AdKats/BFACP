<?php namespace BFACP\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class UsersController extends BaseController
{
    /**
     * User Repository
     * @var BFACP\Repositories\UserRepository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = \App::make('BFACP\Repositories\UserRepository');
    }

    /**
     * Shows the login form
     * @return Illuminate\Support\Facades\View
     */
    public function showLogin()
    {
        return View::make('user.login');
    }

    /**
     * Attemps to login with the given credentials.
     */
    public function login()
    {
        $input = Input::all();

        if ($this->repository->login($input)) {
            return Redirect::intended('/');
        }

        if ($this->repository->isThrottled($input)) {
            $error = Lang::get('confide::confide.alerts.too_many_attempts');
        } elseif ($this->repository->existsButNotConfirmed($input)) {
            $error = Lang::get('confide::confide.alerts.not_confirmed');
        } else {
            $error = Lang::get('confide::confide.alerts.wrong_credentials');
        }

        return Redirect::route('user.login')
            ->withInput(Input::except('password'))
            ->with('error', $error);
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
