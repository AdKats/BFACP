<?php

namespace BFACP\Repositories;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Connection as DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseRepository.
 */
class BaseRepository
{
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
    public $database;

    /**
     * @var Config
     */
    public $config;


    public function __construct()
    {
        $this->user = Auth::user();
        $this->isLoggedIn = Auth::check();
        $this->cache = app(Cache::class);
        $this->request = app(Request::class);
        $this->database = app(DB::class);
        $this->config = app(Config::class);
    }
}
