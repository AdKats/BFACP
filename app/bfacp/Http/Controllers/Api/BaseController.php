<?php namespace BFACP\Http\Controllers\Api;

use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use ControllerTrait;

    public $request;
    public $user;
    public $isLoggedIn;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->user = \App::make('bfadmincp')->user;
        $this->isLoggedIn = \App::make('bfadmincp')->isLoggedIn;
    }
}
