<?php

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

$app = new Illuminate\Foundation\Application;

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

$env = $app->detectEnvironment(array(

	'local' => array('your-machine-name'),

));


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

$app->bindInstallPaths(require __DIR__.'/paths.php');

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

$framework = $app['path.base'].'/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

App::error(function(PDOException $e)
{
    $message = explode(' ', $e->getMessage());
    $dbCode = rtrim($message[1], ']');
    $dbCode = trim($dbCode, '[');

    switch ($dbCode)
    {
        case 1049:
            $userMessage = 'Unknown database - probably config error';
            $userMessage2 = "There seems to be a configuration issue. Please notify the site administrator of the error.";
        break;

        case 2003:
        case 2002:
            $userMessage = 'CONNECTION FAILED';
            $userMessage2 = 'Connection to database could not be established.';
        break;

        case 1040:
            $userMessage = 'CONNECTION FAILED';
            $userMessage2 = 'Too many connections';
        break;

        case 1045:
        case 1044:
            $userMessage = 'ACCESS DENIED';
            $userMessage2 = 'Could not login with supplied user information';
        break;

        default:
            $userMessage = 'Untrapped Error';
            $userMessage2 = $e->getMessage();
        break;
    }

    if(App::runningInConsole())
    {
        die("ERROR: " . $userMessage2);
    }
    else
    {
        die( View::make('error.dberror')->with('errusrmsg', $userMessage)->with('errcode', $e->getCode() . "&nbsp;")->with('errmsg', $e->getMessage())->with('errusrmsg2', $userMessage2) );
    }
});

if(!App::runningInConsole())
{
    // Make sure the storage directory and sub folders are writeable
    if(!is_writable(storage_path()))
    {
        die('All folders under app/storage must be set to 0777');
    }

    // Check and make sure the sessions table exists otherwise create it
    if(!Schema::hasTable('bfadmincp_sessions'))
    {
        DB::statement(File::get(storage_path() . '/sql/add_missing_sessions_table.sql'));
    }
}

if(version_compare(phpversion(), '5.4.0', '<') || !extension_loaded("mcrypt") || !extension_loaded("pdo"))
{
    die(View::make('error.requirement_check_failed'));
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
