<?php

namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Macros.
 */
class Macros extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'macros';
    }
}
