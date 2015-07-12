<?php namespace BFACP\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    /**
     * BFACP\Account\User
     *
     * @var object
     */
    public $user;

    /**
     * Is the user logged in
     *
     * @var boolean
     */
    public $isLoggedIn = false;

    /**
     * Messages to be sent to the view
     *
     * @var array
     */
    public $messages = [];

    public function __construct()
    {
        $bfacp = App::make('bfadmincp');
        $this->user = $bfacp->user;
        $this->isLoggedIn = $bfacp->isLoggedIn;
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
}
