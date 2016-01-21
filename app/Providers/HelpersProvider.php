<?php

namespace BFACP\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * Class HelpersProvider.
 */
class HelpersProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['main'] = $this->app->share(function () {
            return app('BFACP\Helpers\Main');
        });

        $this->app['battlefield'] = $this->app->share(function () {
            return app('BFACP\Helpers\Battlefield');
        });

        $this->app['macros'] = $this->app->share(function () {
            return app('BFACP\Helpers\Macros');
        });

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('MainHelper', 'BFACP\Facades\Main');
            $loader->alias('BattlefieldHelper', 'BFACP\Facades\Battlefield');
            $loader->alias('Macros', 'BFACP\Facades\Macros');
        });
    }
}
