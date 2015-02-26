<?php namespace BFACP\Repositories;

class BaseRepository
{
    /**
     * Instance of Illuminate\Http\Request
     * @var class
     */
    public $request;

    public function __construct()
    {
        $this->request = \App::make('Illuminate\Http\Request');
    }
}
