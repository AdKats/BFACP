<?php

namespace BFACP\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', 'BFACP\Composers\UserComposer');
        view()->composer('*', 'BFACP\Composers\MenuComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
