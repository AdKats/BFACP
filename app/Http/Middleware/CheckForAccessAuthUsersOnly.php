<?php

namespace BFACP\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class CheckForAccessAuthUsersOnly.
 */
class CheckForAccessAuthUsersOnly
{
    /**
     * \Illuminate\Contracts\Foundation\Application.
     * @var object
     */
    protected $app;

    /**
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->app['config']->get('bfacp.site.auth')) {
            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
