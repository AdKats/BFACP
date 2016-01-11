<?php namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

class Main extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'main';
    }
}