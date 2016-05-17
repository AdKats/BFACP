<?php

namespace BFACP\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;

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
     *
     * @internal param Request $request
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->app['config']->get('bfacp.site.auth') && Auth::guest()) {
            $path = explode('/', $request->path());

            $route = (count($path) > 1 ? $path[0].'/'.$path[1] : $path[0]);

            if (! in_array($route, ['login', 'register', 'user/confirm'])) {
                return redirect()->route('user.login');
            }
        }

        return $next($request);
    }
}
