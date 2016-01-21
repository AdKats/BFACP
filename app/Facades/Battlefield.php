<?php

namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Battlefield.
 */
class Battlefield extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'battlefield';
    }
}
