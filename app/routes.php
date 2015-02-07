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
    });

    Route::group(['prefix' => 'servers'], function()
    {
        Route::get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
    });
});

Route::group(['namespace' => 'BFACP\Http\Controllers'], function()
{
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
    Route::group(['prefix' => 'players'], function()
    {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
    	Route::get('{id}/{name}', ['as' => 'player.show', function()
    	{
    		return Redirect::back();
    	}])->where('id', '[0-9]+');
    });
});
