<?php namespace BFACP\Repositories;

use Illuminate\Support\Facades\App;

class BaseRepository
{
    protected $user;

    public function __construct()
    {
        $this->user = App::make('bfadmincp');
    }
}
