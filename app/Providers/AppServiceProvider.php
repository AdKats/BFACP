<?php

namespace BFACP\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Check if cloudflare is being used.
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            putenv('USE_CLOUDFLARE=true');
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        // If application is running in the console manually
        // set the remote address to 127.0.0.1.
        if ($this->app->runningInConsole()) {
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        } else {
            $timestamp = Carbon::now()->subYears(10)->format("D, d M Y H:i:s \G\M\T");
            header('Expires: '.$timestamp);
        }
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
