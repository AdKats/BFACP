<?php

namespace BFACP\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Class CheckForMaintenanceMode.
 */
class CheckForMaintenanceMode
{
    /**
     * \Illuminate\Http\Request.
     * @var object
     */
    protected $request;

    /**
     * \Illuminate\Contracts\Foundation\Application.
     * @var object
     */
    protected $app;

    /**
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $list = env('IP_WHITELIST', false);

        if (! $list) {
            $whitelist = [];
        } else {
            $whitelist = explode('|', $list);
        }

        if ($this->app->isDownForMaintenance()) {
            if (! in_array($this->request->getClientIp(), $whitelist)) {
                return response()->view('system.maintenance', [], 503);
            }

            putenv('APP_DOWN=true');
        } else {
            putenv('APP_DOWN=false');
        }

        return $next($request);
    }
}
