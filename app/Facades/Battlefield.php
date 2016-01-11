<?php namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

class Battlefield extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'battlefield';
    }
}