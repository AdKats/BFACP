<?php

Route::get('test', function () {
    Auth::loginUsingId(2);

    $input = [
        'ban_type'    => 8,
        'ban_server'  => 13,
        'ban_message' => 'Event Fire Testing 2'
    ];

    $player = BFACP\Battlefield\Player::find(185477);

    $response = Event::fire('player.ban', [
        $input,
        $player,
        $player->ban
    ]);

    return $response;
});

/**
 * Route Model Bindings
 */
Route::model('player', 'BFACP\Battlefield\Player');

/**
 * Route API Registering
 */
Route::api(['namespace' => 'BFACP\Http\Controllers\Api', 'version' => 'v1'], function () {

    /*===================================
    =            API Players            =
    ===================================*/

    Route::group(['prefix' => 'players'], function () {
        Route::get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
        Route::get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])->where('id', '[0-9]+');
        Route::get('{id}/records', ['as' => 'api.players.show.records', 'uses' => 'PlayersController@showRecords'])->where('id', '[0-9]+');
        Route::get('{id}/chatlogs', ['as' => 'api.players.show.chatlogs', 'uses' => 'PlayersController@showChatlogs'])->where('id', '[0-9]+');
    });

    /*=====================================
    =            API Battlelog            =
    =====================================*/

    Route::group(['prefix' => 'battlelog'], function () {
        Route::group(['prefix' => 'players'], function () {
            Route::get('{player}/weapons', ['as' => 'api.battlelog.players.weapons', function (BFACP\Battlefield\Player $player) {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getWeaponStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/overview', ['as' => 'api.battlelog.players.overview', function (BFACP\Battlefield\Player $player) {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getOverviewStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/vehicles', ['as' => 'api.battlelog.players.vehicles', function (BFACP\Battlefield\Player $player) {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getVehicleStats(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/reports', ['as' => 'api.battlelog.players.reports', function (BFACP\Battlefield\Player $player) {
                $battlelog = App::make('BFACP\Libraries\Battlelog\BPlayer', [$player]);

                return MainHelper::response($battlelog->getBattleReports(), null, null, null, false, true);
            }])->where('player', '[0-9]+');

            Route::get('{player}/acs', ['as' => 'api.battlelog.players.acs', function (BFACP\Battlefield\Player $player) {
                $acs = App::make('BFACP\Libraries\AntiCheat', [$player]);
                $data = $acs->parse($acs->battlelog->getWeaponStats())->get();
                return MainHelper::response($data, null, null, null, false, true);
            }])->where('player', '[0-9]+');
        });
    });

    /*================================
    =            API Bans            =
    ================================*/

    Route::group(['prefix' => 'bans'], function () {
        Route::get('latest', ['as' => 'api.bans.latest', 'uses' => 'BansController@latest']);
        Route::get('stats', ['as' => 'api.bans.stats', 'uses' => 'BansController@stats']);

        Route::group(['prefix' => 'metabans'], function () {
            Route::get('/', ['as' => 'api.bans.metabans.index', 'uses' => 'MetabansController@getIndex']);
            Route::get('feed', ['as' => 'api.bans.metabans.feed', 'uses' => 'MetabansController@getFeed']);
            Route::get('assessments', ['as' => 'api.bans.metabans.assessments', 'uses' => 'MetabansController@getAssessments']);
            Route::get('feed_assessments', ['as' => 'api.bans.metabans.feed_assessments', 'uses' => 'MetabansController@getFeedAssessments']);
        });
    });

    /*===================================
    =            API Servers            =
    ===================================*/

    Route::group(['prefix' => 'servers'], function () {
        Route::get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
        Route::get('scoreboard/{id}', ['as' => 'api.servers.scoreboard', 'uses' => 'ServersController@scoreboard'])->where('id', '[0-9]+');
        Route::get('scoreboard/roundstats/{id}', ['as' => 'api.servers.scoreboard.roundstats', 'uses' => 'ServersController@scoreboardExtra'])->where('id', '[0-9]+');
        Route::get('chat/{id}', ['as' => 'api.servers.chat', 'uses' => 'ServersController@chat'])->where('id', '[0-9]+');
        Route::post('scoreboard/admin', ['as' => 'api.servers.scoreboard.admin', 'uses' => 'ServersController@scoreboardAdmin']);
    });

    /*====================================
    =            API Chatlogs            =
    ====================================*/

    Route::group(['prefix' => 'chatlogs'], function () {
        Route::get('/', ['as' => 'api.chatlogs.index', 'uses' => 'ChatlogController@getIndex']);
    });
});

/**
 * Route Application Registering
 */
Route::group(['namespace' => 'BFACP\Http\Controllers'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    /*===================================
    =            Auth Routes            =
    ===================================*/

    Route::get('login', ['as' => 'user.login', 'uses' => 'UsersController@showLogin', 'before' => 'guest']);
    Route::get('logout', ['as' => 'user.logout', 'uses' => 'UsersController@logout', 'before' => 'auth']);
    Route::get('register', ['as' => 'user.register', 'uses' => 'UsersController@showSignup', 'before' => 'guest|user.register.enabled']);
    Route::get('user/confirm/{code}', ['as' => 'user.confirm', 'uses' => 'UsersController@confirm', 'before' => 'guest']);
    Route::post('login', ['as' => 'user.login.post', 'uses' => 'UsersController@login', 'before' => 'guest|csrf']);
    Route::post('register', ['as' => 'user.register.post', 'uses' => 'UsersController@signup', 'before' => 'guest|csrf']);

    /*-----  End of Auth Routes  ------*/

    Route::get('chatlogs', ['as' => 'chatlog.search', 'uses' => 'ChatlogController@index', 'before' => 'chatlogs']);

    Route::group(['prefix' => 'players'], function () {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
        Route::get('{id}/{name?}', ['as' => 'player.show', 'uses' => 'PlayersController@profile'])->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'servers'], function () {
        Route::get('live', ['as' => 'servers.live', 'uses' => 'HomeController@scoreboard']);
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::group(['prefix' => 'adkats', 'namespace' => 'AdKats'], function () {

            // AdKats Settings
            Route::resource('settings', 'SettingsController', [
                'names' => [
                    'index' => 'admin.adkats.settings.index',
                    'edit'  => 'admin.adkats.settings.edit'
                ],
                'only'  => ['index', 'edit']
            ]);

            // AdKats Bans
            Route::resource('bans', 'BansController', [
                'names' => [
                    'index'   => 'admin.adkats.bans.index',
                    'edit'    => 'admin.adkats.bans.edit',
                    'update'  => 'admin.adkats.bans.update',
                    'destroy' => 'admin.adkats.bans.destroy',
                    'store'   => 'admin.adkats.bans.store',
                    'create'  => 'admin.adkats.bans.create'
                ],
                'only'  => ['index', 'edit', 'update', 'destroy', 'store', 'create']
            ]);

            // AdKats Users
            Route::resource('users', 'UsersController', [
                'names' => [
                    'index'   => 'admin.adkats.users.index',
                    'edit'    => 'admin.adkats.users.edit',
                    'store'   => 'admin.adkats.users.store',
                    'update'  => 'admin.adkats.users.update',
                    'destroy' => 'admin.adkats.users.destroy'
                ],
                'only'  => ['index', 'edit', 'update', 'destroy', 'store']
            ]);

            Route::resource('special_players', 'SpecialPlayersController', [
                'names' => [
                    'index'  => 'admin.adkats.special_players.index',
                    'update' => 'admin.adkats.special_players.update'
                ],
                'only'  => ['index', 'update']
            ]);
        });

        Route::group(['prefix' => 'site', 'namespace' => 'Site'], function () {
            Route::resource('users', 'UsersController', [
                'names' => [
                    'index'   => 'admin.site.users.index',
                    'edit'    => 'admin.site.users.edit',
                    'destroy' => 'admin.site.users.destroy',
                    'update'  => 'admin.site.users.update'
                ],
                'only'  => ['index', 'edit', 'destroy', 'update']
            ]);

            Route::resource('roles', 'RolesController', [
                'names' => [
                    'index'   => 'admin.site.roles.index',
                    'edit'    => 'admin.site.roles.edit',
                    'destroy' => 'admin.site.roles.destroy',
                    'update'  => 'admin.site.roles.update',
                    'create'  => 'admin.site.roles.create',
                    'store'   => 'admin.site.roles.store'
                ],
                'only'  => ['index', 'edit', 'destroy', 'update', 'create', 'store']
            ]);
        });

        Route::get('updater', ['as' => 'admin.updater.index', 'uses' => 'UpdaterController@index']);
    });
});

/**
 * Route Permissions
 */

/*===================================
=            AdKats Bans            =
===================================*/
Entrust::routeNeedsPermission('admin/adkats/bans', 'admin.adkats.bans.view');
Entrust::routeNeedsPermission('admin/adkats/bans/create', 'admin.adkats.bans.create');
Entrust::routeNeedsPermission('admin/adkats/bans/*', 'admin.adkats.bans.edit');

/*====================================
=            AdKats Users            =
====================================*/
Entrust::routeNeedsPermission('admin/adkats/users', 'admin.adkats.user.view');
Entrust::routeNeedsPermission('admin/adkats/users/*', 'admin.adkats.user.edit');

/*============================================
=            AdKats Special Users            =
============================================*/
Entrust::routeNeedsPermission('admin/adkats/special_players', 'admin.adkats.special.view');
Entrust::routeNeedsPermission('admin/adkats/special_players/*', 'admin.adkats.special.edit');

/*=======================================
=            AdKats Settings            =
=======================================*/
Entrust::routeNeedsPermission('admin/adkats/settings', 'admin.adkats.settings.edit');
Entrust::routeNeedsPermission('admin/adkats/settings/*', 'admin.adkats.settings.edit');

/*==================================
=            Site Users            =
==================================*/
Entrust::routeNeedsPermission('admin/site/users', 'admin.site.users');
Entrust::routeNeedsPermission('admin/site/users/*', 'admin.site.users');

/*==================================
=            Site Roles            =
==================================*/
Entrust::routeNeedsPermission('admin/site/roles', 'admin.site.roles');
Entrust::routeNeedsPermission('admin/site/roles/*', 'admin.site.roles');

/*================================================
=            Require the Menu Builder            =
================================================*/

require $app['path.base'] . '/app/menu.php';
