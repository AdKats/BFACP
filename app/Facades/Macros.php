<?php

namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Macros
 * @package BFACP\Facades
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
