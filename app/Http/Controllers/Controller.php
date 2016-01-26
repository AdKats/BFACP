<?php

namespace BFACP\Http\Controllers;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\Connection as DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Config\Repository as Config;

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
     * @param Cache   $cache
     * @param Request $request
     * @param DB      $database
     * @param Config  $config
     */
    public function __construct(Cache $cache, Request $request, DB $database, Config $config)
    {
        $this->user = Auth::user();
        $this->isLoggedIn = Auth::check();
        $this->cache = $cache;
        $this->request = $request;
        $this->db = $database;
        $this->config = $config;
    }
}
