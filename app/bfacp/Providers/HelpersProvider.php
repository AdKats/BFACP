<?php namespace BFACP\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class HelpersProvider extends ServiceProvider
{
    public function register()
    {
        $this->app['main'] = $this->app->share(function ($app) {
            return app('BFACP\Helpers\Main');
        });

        $this->app['battlefield'] = $this->app->share(function ($app) {
            return app('BFACP\Helpers\Battlefield');
        });

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('MainHelper', 'BFACP\Facades\Main');
            $loader->alias('BattlefieldHelper', 'BFACP\Facades\Battlefield');
        });
    }
}