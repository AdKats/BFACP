<?php

namespace BFACP\Http\Middleware;

use Closure;

/**
 * Class IpWhitelisted.
 */
class IpWhitelisted
{
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $ips = explode('|', env('IP_WHITELIST', ''));

        $clientIP = $_SERVER['REMOTE_ADDR'];

        if (! in_array($clientIP, $ips)) {
            return abort(403);
        }

        return $next($request);
    }
}
