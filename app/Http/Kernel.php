<?php

namespace BFACP\Http;

use BFACP\Http\Middleware\Authenticate;
use BFACP\Http\Middleware\CheckForAccessAuthUsersOnly;
use BFACP\Http\Middleware\CheckForMaintenanceMode;
use BFACP\Http\Middleware\EncryptCookies;
use BFACP\Http\Middleware\IpWhitelisted;
use BFACP\Http\Middleware\RedirectIfAuthenticated;
use BFACP\Http\Middleware\Secure;
use BFACP\Http\Middleware\VerifyCsrfToken;
use BFACP\Http\Middleware\ViewableChatlogs;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Zizaco\Entrust\Middleware\EntrustAbility;
use Zizaco\Entrust\Middleware\EntrustPermission;
use Zizaco\Entrust\Middleware\EntrustRole;

/**
 * Class Kernel.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            CheckForAccessAuthUsersOnly::class,
        ],
        'api' => [
            'throttle:120,1',
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'        => Authenticate::class,
        'auth.basic'  => AuthenticateWithBasicAuth::class,
        'guest'       => RedirectIfAuthenticated::class,
        'throttle'    => ThrottleRequests::class,
        'role'        => EntrustRole::class,
        'permission'  => EntrustPermission::class,
        'ability'     => EntrustAbility::class,
        'whitelisted' => IpWhitelisted::class,
        'chatlogs'    => ViewableChatlogs::class,
    ];

    public function bootstrap()
    {
        parent::bootstrap();

        if ($this->app['config']->get('bfacp.site.ssl')) {
            $this->middleware[] = Secure::class;
        }
    }
}
