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
        $bfacp = App::make('bfadmincp');
        $this->isLoggedIn = $bfacp->isLoggedIn;
        $this->request = App::make('Illuminate\Http\Request');
        $this->user = $bfacp->user;
    }
}
