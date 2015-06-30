<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
 */

App::before(function ($request) {
    // CloudFlare IP addresses to trust
    // Proxies obtained from https://www.cloudflare.com/ips-v4
    Request::setTrustedProxies(Cache::remember('cloudflare.ips', 24 * 60 * 7, function () {
        $request = App::make('GuzzleHttp\Client')->get('https://www.cloudflare.com/ips-v4');
        $response = $request->getBody();
        return explode("\n", $response);
    }));

    // If request is not secured and force secured connection is enabled
    // then we need to redirect the user to a secure link.
    if (!Request::secure() && Config::get('bfacp.site.ssl') && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
        $path = Request::path();

        if (strlen(Request::server('QUERY_STRING')) > 0) {
            $path .= '?' . Request::server('QUERY_STRING');
        }

        $status = in_array(Request::getMethod(), ['POST', 'PUT', 'DELETE']) ? 307 : 302;

        return Redirect::secure($path, $status);
    }

    if (Config::get('bfacp.site.auth') && !Auth::check()) {
        $path = explode('/', Request::path());

        if (count($path) > 1) {
            $route = $path[0] . '/' . $path[1];
        } else {
            $route = $path[0];
        }

        if (!in_array($route, ['login', 'register', 'user/confirm'])) {
            return Redirect::route('user.login');
        }
    }

    View::share('bfacp', App::make('bfadmincp'));
});

App::after(function ($request, $response) {
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
 */

Route::filter('auth', function () {
    if (Auth::guest()) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', 401);
        } else {
            return Redirect::guest(route('user.login'));
        }
    }
});

Route::filter('auth.basic', function () {
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
 */

Route::filter('guest', function () {
    if (Auth::check()) {
        return Redirect::route('home');
    }
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
 */

Route::filter('csrf', function () {
    if (Session::token() !== Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException();
    }
});

/*
|--------------------------------------------------------------------------
| Custom Filters
|--------------------------------------------------------------------------
 */

Route::filter('user.register.enabled', function () {
    if (!Config::get('bfacp.site.registration')) {
        return Redirect::route('home');
    }
});

Route::filter('chatlogs', function () {
    if ((Auth::guest() && !Config::get('bfacp.site.chatlogs.guest')) || (Auth::check() && !Auth::user()->ability(null, 'chatlogs'))) {
        return Redirect::route('home');
    }
});
