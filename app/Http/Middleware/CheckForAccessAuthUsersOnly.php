<?php

namespace BFACP\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;

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
        $currentRoute = Route::route()->getName();

        if ($this->app['config']->get('bfacp.site.auth') && Auth::guest() && in_array($currentRoute,
                ['user.login', 'user.register', 'user.logout'])
        ) {
            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
