<?php

namespace BFACP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Main.
 */
class Main extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'main';
    }
}
