<?php

namespace BFACP\Repositories;

use Illuminate\Support\Facades\Auth;

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

    public function __construct()
    {
        $this->user = Auth::user();
        $this->isLoggedIn = Auth::check();
    }
}
