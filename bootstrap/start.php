<?php

if (!defined('BFACP_VERSION')) {
    define('BFACP_VERSION', '2.0.2');
}

if (php_sapi_name() != "cli") {
    header(sprintf('Expires: %s', Carbon\Carbon::now()->subYears(10)->format("D, d M Y H:i:s \G\M\T")));
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
 */

$app = new Illuminate\Foundation\Application();

/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
 */

$app->bindInstallPaths(require __DIR__ . '/paths.php');

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
 */

$env = $app->detectEnvironment(function () use ($app) {
    return require_once __DIR__ . '/environment.php';
});

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load this Illuminate application. We will keep this in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
 */

$framework = $app['path.base'] .
    '/vendor/laravel/framework/src';

require $framework . '/Illuminate/Foundation/start.php';

if (!$app->runningInConsole()) {
    $setupFilePath = $app['path.base'] . '/app/bfacp/setup.php';
    $jsBuildsPath = $app['path.public'] . '/js/builds';

    if (version_compare(phpversion(), '5.5.0', '<') || !extension_loaded('mcrypt') || !extension_loaded('pdo')) {
        die(View::make('system.requirements', ['required_php_version' => '5.5.0']));
    }

    if (file_exists($setupFilePath) && !in_array(App::environment(), ['local', 'testing'])) {
        require_once $setupFilePath;

        if (!unlink($setupFilePath)) {
            die(sprintf('Please delete installer located at "%s"', $setupFilePath));
        }
    }

    $_SERVER['REMOTE_ADDR'] = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
} else {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}

App::singleton('bfadmincp', function () {
    $app = new stdClass();

    $app->isLoggedIn = Auth::check();
    $app->user = null;

    if ($app->isLoggedIn) {
        $app->user = Auth::user();
        App::setLocale($app->user->setting->lang);
    }

    return $app;
});

App::singleton('geo', function () {
    return App::make('BFACP\Repositories\GeoRepository');
});

App::singleton('guzzle', function () {
    return App::make('GuzzleHttp\Client');
});

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
 */

return $app;
