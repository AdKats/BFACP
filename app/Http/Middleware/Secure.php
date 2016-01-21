<?php

namespace BFACP\Http\Middleware;

use Closure;

/**
 * Secure
 * Redirects any non-secure requests to their secure counterparts.
 *
 * @param request The request object.
 * @param $next   The next closure.
 *
 * @return redirects to the secure counterpart of the requested uri.
 */
class Secure
{

    /**
     * @param         $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (! $request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
