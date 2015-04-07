<?php

/**
 * Route Model Bindings
 */
Route::model('player', 'BFACP\Battlefield\Player');

/**
 * Route API Registering
 */
Route::api(['namespace' => 'BFACP\Http\Controllers\Api', 'version' => 'v1'], function()
{
    Route::group(['prefix' => 'players'], function()
    {
        Route::get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
        Route::get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])->where('id', '[0-9]+');
        Route::get('{id}/records', ['api.players.show.records', 'uses' => 'PlayersController@showRecords'])->where('id', '[0-9]+');
        Route::get('{id}/chatlogs', ['api.players.show.chatlogs', 'uses' => 'PlayersController@showChatlogs'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'battlelog'], function()
    {
        Route::group(['prefix' => 'players'], function()
        {
            Route::get('{player}/weapons', ['as' => 'api.battlelog.players.weapons', function(BFACP\Battlefield\Player $player)
            {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getWeaponStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/overview', ['as' => 'api.battlelog.players.overview', function(BFACP\Battlefield\Player $player)
            {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getOverviewStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/vehicles', ['as' => 'api.battlelog.players.vehicles', function(BFACP\Battlefield\Player $player)
            {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getVehicleStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/reports', ['as' => 'api.battlelog.players.reports', function(BFACP\Battlefield\Player $player)
            {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getBattleReports(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/acs', ['as' => 'api.battlelog.players.acs', function(BFACP\Battlefield\Player $player)
            {
                $acs = App::make('BFACP\Libraries\AntiCheat', [$player]);
                $data = $acs->parse($acs->battlelog->getWeaponStats())->get();
                return MainHelper::response($data, null, null, null, false, true);
            }])->where('player', '[0-9]+');
        });
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


/**
 * Route Application Registering
 */
Route::group(['namespace' => 'BFACP\Http\Controllers'], function()
{
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::get('login', ['as' => 'user.login', 'uses' => 'UsersController@showLogin', 'before' => 'guest']);
    Route::get('logout', ['as' => 'user.logout', 'uses' => 'UsersController@logout', 'before' => 'auth']);
    Route::get('register', ['as' => 'user.register', 'uses' => 'UsersController@showLogin', 'before' => 'guest|user.register.enabled']);
    Route::post('login', ['as' => 'user.login.post', 'uses' => 'UsersController@login', 'before' => 'guest|csrf']);

    Route::get('chatlogs', ['as' => 'chatlog.search', 'uses' => 'HomeController@chatlogs']);

    Route::group(['prefix' => 'players'], function()
    {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
    	Route::get('{id}/{name?}', ['as' => 'player.show', 'uses' => 'PlayersController@profile'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'servers'], function()
    {
        Route::get('live', ['as' => 'servers.live', 'uses' => 'HomeController@scoreboard']);
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function()
    {
        Route::group(['prefix' => 'adkats', 'namespace' => 'AdKats'], function()
        {
            // AdKats Bans Routes
            Route::resource('bans', 'BansController', [
                'names' => [
                    'index'   => 'admin.adkats.bans.index',
                    'edit'    => 'admin.adkats.bans.edit',
                    'update'  => 'admin.adkats.bans.update',
                    'destroy' => 'admin.adkats.bans.destroy'
                ],
                'only' => ['index', 'edit', 'update', 'destroy']
            ]);
        });

        Route::get('updater', ['as' => 'admin.updater.index', 'uses' => 'UpdaterController@index']);
    });
});

/**
 * Route Permissions
 */
Entrust::routeNeedsPermission('admin/adkats/bans', 'admin.adkats.bans.view');
Entrust::routeNeedsPermission('admin/adkats/bans/*', 'admin.adkats.bans.edit');
Entrust::routeNeedsPermission('chatlogs', 'chatlogs');
