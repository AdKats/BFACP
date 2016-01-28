<?php

namespace BFACP\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Connection as DB;

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

    public function __construct(Cache $cache, Request $request, DB $database, Config $config)
    {
        $this->user = Auth::user();
        $this->isLoggedIn = Auth::check();
        $this->cache = $cache;
        $this->request = $request;
        $this->database = $database;
        $this->config = $config;
    }
}
