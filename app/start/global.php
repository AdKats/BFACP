<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

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

Log::useFiles(storage_path().'/logs/laravel.log');

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

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
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

App::down(function()
{
	return Response::make("Be right back!", 503);
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

require app_path().'/filters.php';


/*
|--------------------------------------------------------------------------
| Set the application URL
|--------------------------------------------------------------------------
|
| This will set the URL used by the console to properly generate URLs.
|
| More info about it can be found in the app.php config file under
| application url section.
|
*/

Config::set('app.url', (Request::secure() ? 'https://' : 'http://') . (Request::server('SERVER_NAME') ?: 'localhost'));

/*
|--------------------------------------------------------------------------
| Load the event listeners
|--------------------------------------------------------------------------
|
| Load the events file for the application
|
*/

require app_path().'/events.php';

/*
|--------------------------------------------------------------------------
| Fire events that should run on load
|--------------------------------------------------------------------------
|
*/

if(Request::format() != 'json' || Request::is('api/*') === FALSE)
{
    if(Auth::check())
    {
        $lastSeen = Event::fire('user.lastseen', [Auth::user()]);
    }
}

/*
|--------------------------------------------------------------------------
| Error Handling
|--------------------------------------------------------------------------
|
*/

App::missing(function($exception)
{
    if(Request::is('api/*'))
        return Helper::response('error', 'Resource Not Found', [], 404);

    View::share('title', 'Page Not Found');
    return Response::view('error.404', [], 404);
});
