<?php

Route::api(['namespace' => 'BFACP\Http\Controllers\Api', 'version' => 'v1'], function()
{
    Route::group(['prefix' => 'players'], function()
    {
        Route::get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
        Route::get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])
            ->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'bans'], function()
    {
        Route::get('latest', ['as' => 'api.bans.latest', 'uses' => 'BansController@latest']);
        Route::get('stats', ['as' => 'api.bans.stats', 'uses' => 'BansController@stats']);

        Route::group(['prefix' => 'metabans'], function()
        {
            Route::get('/', ['as' => 'api.bans.metabans.index', 'uses' => 'MetabansController@getIndex']);
            Route::get('feed', ['as' => 'api.bans.metabans.feed', 'uses' => 'MetabansController@getFeed']);
            Route::get('assessments', ['as' => 'api.bans.metabans.assessments', 'uses' => 'MetabansController@getAssessments']);
            Route::get('feed_assessments', ['as' => 'api.bans.metabans.feed_assessments', 'uses' => 'MetabansController@getFeedAssessments']);
        });
    });

    Route::group(['prefix' => 'servers'], function()
    {
        Route::get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
        Route::get('scoreboard/{id}', ['as' => 'api.servers.scoreboard', 'uses' => 'ServersController@scoreboard'])->where('id', '[0-9]+');
        Route::get('scoreboard/roundstats/{id}', ['as' => 'api.servers.scoreboard.roundstats', 'uses' => 'ServersController@scoreboardExtra'])
            ->where('id', '[0-9]+');
        Route::get('chat/{id}', ['as' => 'api.servers.chat', 'uses' => 'ServersController@chat'])->where('id', '[0-9]+');

        Route::post('scoreboard/admin', ['as' => 'api.servers.scoreboard.admin', 'uses' => 'ServersController@scoreboardAdmin']);
    });

    Route::group(['prefix' => 'chatlogs'], function()
    {
        Route::get('/', ['as' => 'api.chatlogs.index', 'uses' => 'ChatlogController@getIndex']);
    });
});

Route::group(['namespace' => 'BFACP\Http\Controllers'], function()
{
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::get('chatlogs', ['as' => 'chatlog.search', 'uses' => 'HomeController@chatlogs']);

    Route::group(['prefix' => 'players'], function()
    {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
    	Route::get('{id}/{name?}', ['as' => 'player.show', 'uses' => 'PlayersController@profile'])
            ->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'servers'], function()
    {
        Route::get('live', ['as' => 'servers.live', 'uses' => 'HomeController@scoreboard']);
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function()
    {
        Route::group(['prefix' => 'adkats', 'namespace' => 'AdKats'], function()
        {
            Route::get('locale', ['as' => 'admin.adkats.locale.index', 'uses' => 'LocaleController@showIndex']);
            Route::post('locale', ['as' => 'admin.adkats.locale.save', 'uses' => 'LocaleController@save']);
        });

        Route::get('updater', ['as' => 'admin.updater.index', 'uses' => 'UpdaterController@index']);
    });
});
