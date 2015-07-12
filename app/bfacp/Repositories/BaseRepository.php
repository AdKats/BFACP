<?php namespace BFACP\Repositories;

use Illuminate\Support\Facades\App;

class BaseRepository
{
    protected $user = null;

    protected $isLoggedIn = false;

    public function __construct()
    {
        $bfacp = App::make('bfadmincp');

        $this->user = $bfacp->user;
        $this->isLoggedIn = $bfacp->isLoggedIn;
    }
}
