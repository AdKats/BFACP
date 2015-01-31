<?php

Route::api(['namespace' => 'BFACP\Http\Controllers\Api', 'version' => 'v1'], function()
{
    Route::get('players', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
    Route::get('players/{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])
        ->where('id', '[0-9]+');
});
