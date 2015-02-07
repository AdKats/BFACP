<?php namespace BFACP\Http\Controllers\Api;

use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;

class BaseController extends Controller
{
    use ControllerTrait;

    protected $request;
    protected $user;
    protected $isLoggedIn;

    public function __construct()
    {
        $this->isLoggedIn = App::make('bfadmincp')->isLoggedIn;
        $this->request    = App::make('Illuminate\Http\Request');
        $this->user       = App::make('bfadmincp')->user;
    }
}
