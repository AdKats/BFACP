<?php namespace BFACP\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use MainHelper;

class UsersController extends BaseController
{
    public function showLogin()
    {
        return View::make('user.login');
    }
}
