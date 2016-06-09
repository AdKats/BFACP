<?php

namespace BFACP\Http\Controllers;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Connection as DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Log\Writer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Class Controller.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Stores the currently logged in user.
     *
     * @var \BFACP\Account\User
     */
    protected $user;

    /**
     * Is the user logged in?
     *
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * Any messages to be pushed to the view.
     *
     * @var array
     */
    public $messages = [];

    /**
     * Any error messages to be pushed to the view.
     *
     * @var array
     */
    public $errors = [];

    /**
     * @var Cache
     */
    public $cache;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var DB
     */
    public $db;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var PusherManager
     */
    public $pusher;

    /**
     * @var Writer
     */
    public $log;


    public function __construct()
    {
        $this->user = Auth::user();
        $this->isLoggedIn = Auth::check();
        $this->cache = app(Cache::class);
        $this->request = app(Request::class);
        $this->db = app(DB::class);
        $this->config = app(Config::class);
        $this->log = app(Writer::class);
    }
}
