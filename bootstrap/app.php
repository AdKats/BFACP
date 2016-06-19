<?php

if (! defined('BFACP_VERSION')) {
    define('BFACP_VERSION', '2.1.0');
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

$app = new Illuminate\Foundation\Application(realpath(__DIR__.'/../'));

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(Illuminate\Contracts\Http\Kernel::class, BFACP\Http\Kernel::class);

$app->singleton(Illuminate\Contracts\Console\Kernel::class, BFACP\Console\Kernel::class);

$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, BFACP\Exceptions\Handler::class);

$app->singleton('Guzzle', GuzzleHttp\Client::class);

$app->singleton('GeoIP', function () {
    $cityBinary = app_path('ThirdParty/GeoIP2/GeoLite2-City.mmdb');

    if (file_exists($cityBinary)) {
        return new GeoIp2\Database\Reader($cityBinary);
    }
});

if (! $app->runningInConsole()) {
    $setupFilePath = base_path('installer/install.php');
    $jsBuildDirectoryPath = public_path('js/builds');
    $minPHPVersion = '5.5.9';
    $versionCompare = version_compare(phpversion(), $minPHPVersion, '<');

    if ($versionCompare || ! extension_loaded('mcrypt') || ! extension_loaded('pdo')) {
        die(view('system.requirements', ['required_php_version' => $minPHPVersion]));
    }

    /*
    if (file_exists($setupFilePath) && ! $app->isLocal()) {
        require_once $setupFilePath;
        if (! unlink($setupFilePath)) {
            die(sprintf('Please delete installer located at "%s"', $setupFilePath));
        }
    }
    */
}

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
