<?php

namespace BFACP\Providers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Class CloudflareProvider.
 */
class CloudflareProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! env('USE_CLOUDFLARE', false)) {
            return;
        }

        $request = $this->app['request'];
        $cache = $this->app['cache'];

        /*
         * Downloads the cloudflare ip addresses and caches the result for 1 week. This is needed if
         * your site is behind the cloudflare services. You can disable this by setting USE_CLOUDFLARE
         * in the .env file from false to true.
         */
        try {
            $proxies = $cache->remember('cloudflare.ips', 24 * 60 * 7, function () use (&$request) {
                $guzzle = app('Guzzle');
                $ipv4 = $guzzle->get('https://www.cloudflare.com/ips-v4');
                $ipv6 = $guzzle->get('https://www.cloudflare.com/ips-v6');

                $ipv4 = array_filter(explode("\n", $ipv4->getBody()));

                if (env('APP_DEBUG')) {
                    Log::debug('Cloudflare IPv4 Loaded', [
                        'ips' => $ipv4,
                    ]);
                }

                $ipv6 = array_filter(explode("\n", $ipv6->getBody()));

                if (env('APP_DEBUG')) {
                    Log::debug('Cloudflare IPv6 Loaded', [
                        'ips' => $ipv6,
                    ]);
                }

                return array_merge($ipv4, $ipv6);
            });

            $request->setTrustedProxies($proxies);
        } catch (Exception $e) {
            $cache->forget('cloudflare.ips');
            Log::warning('Unable to setup cloudflare trusted proxies.', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
