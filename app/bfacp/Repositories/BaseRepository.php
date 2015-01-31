<?php namespace BFACP\Repositories;

use Illuminate\Http\Request;

class BaseRepository
{
    /**
     * Instance of Illuminate\Http\Request
     * @var class
     */
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
