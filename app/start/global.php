<?php

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
 */

$logFile = sprintf('log-%s.txt', php_sapi_name());

Log::useDailyFiles(storage_path() . '/logs/' . $logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
 */

App::error(function (Exception $exception, $code) {
    switch ($code) {
        case 403:
            if (Auth::check()) {
                if (Request::is('api/*')) {
                    return MainHelper::response(null, 'Access Forbidden!', 'error', 403);
                }

                return Redirect::route('home')->withErrors([
                    'Access Forbidden!',
                ]);
            }

            return Redirect::guest(route('user.login'));
            break;

        case 405:
            if (Request::is('api/*')) {
                return MainHelper::response(null, 'No Method Available', 'error', 405);
            }

            return Redirect::intended(route('home'))->withErrors([
                'No Method Available',
            ]);
            break;
    }

    Log::error($exception);

    if (App::runningInConsole()) {
        die($exception->getMessage() . PHP_EOL);
    } else {
        if ($exception instanceof PDOException) {
            $clientIp = $_SERVER['REMOTE_ADDR'];
            $whitelist = getenv('IP_WHITELIST') !== false ? explode('|', getenv('IP_WHITELIST')) : [];
            $isWhitelisted = in_array($clientIp, $whitelist);

            if (Request::ajax() || Request::is('api/*')) {
                return MainHelper::response(($isWhitelisted ? [$exception->getMessage()] : null), 'Database Error!',
                    'error', 500);
            }

            return Response::view('system.db', compact('exception', 'isWhitelisted'), 500);
        }

        if (!Config::get('app.debug')) {
            if (Request::ajax() || Request::is('api/*')) {
                return MainHelper::response(null, $exception->getMessage(), 'error', 500);
            } else {
                View::share('page_title', 'Fatal Error');

                return Response::view('system.error', compact('exception', 'code'), 500);
            }
        }

        if (Request::is('api/*')) {
            return MainHelper::response([
                [
                    'message' => $exception->getMessage(),
                    'line'    => $exception->getLine(),
                    'file'    => $exception->getFile(),
                ],
            ], 'Fatal Error', 'error', 500);
        }
    }
});

App::missing(function () {

    if (Request::is('api/*')) {
        return MainHelper::response(null, 'Resource Not Found', 'error', 404);
    }

    View::share('page_title', 'Page Not Found');

    return Response::view('system.notfound', [], 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
 */

App::down(function () {
    $clientIp = $_SERVER['REMOTE_ADDR'];
    $whitelist = getenv('IP_WHITELIST') !== false ? explode('|', getenv('IP_WHITELIST')) : [];

    if (!in_array($clientIp, $whitelist)) {
        return Response::view('system.maintenance', [], 503);
    }

    View::share('appdown', true);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
 */

require app_path() . '/filters.php';
require app_path() . '/bfacp/macros.php';
require app_path() . '/bfacp/events.php';
require app_path() . '/bfacp/composers.php';
