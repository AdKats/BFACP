<?php

/**
 * Route Model Bindings
 */
Route::model('player', 'BFACP\Battlefield\Player');

/**
 * Route API Registering
 */
Route::api(['namespace' => 'BFACP\Http\Controllers\Api', 'version' => 'v1'], function () {

    /*===================================
    =            API Resources          =
    ===================================*/
    Route::group(['prefix' => 'helpers'], function () {
        Route::group(['prefix' => 'adkats'], function () {
            Route::get('special_groups', 'HelpersController@getSpecialGroups');
        });
        Route::get('online/admins', 'HelpersController@onlineAdmins');
        Route::get('ip/{addy}', 'HelpersController@iplookup');
        Route::get('squads', function () {
            $squads = [];
            for ($i = 0; $i <= 32; $i++) {
                $squads[] = [
                    'id'   => $i,
                    'name' => BattlefieldHelper::squad($i),
                ];
            }

            return $squads;
        });
    });

    /*===================================
    =            API Pusher             =
    ===================================*/

    Route::controller('pusher', 'PusherController');

    /*===================================
    =            API Players            =
    ===================================*/

    Route::group(['prefix' => 'players'], function () {
        Route::get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
        Route::get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])->where('id', '[0-9]+');
        Route::get('{id}/records',
            ['as' => 'api.players.show.records', 'uses' => 'PlayersController@showRecords'])->where('id', '[0-9]+');
        Route::get('{id}/chatlogs',
            ['as' => 'api.players.show.chatlogs', 'uses' => 'PlayersController@showChatlogs'])->where('id', '[0-9]+');
        Route::get('{id}/sessions',
            ['as' => 'api.players.show.sessions', 'uses' => 'PlayersController@showSessions'])->where('id', '[0-9]+');
    });

    /*=====================================
    =            API Battlelog            =
    =====================================*/

    Route::group(['prefix' => 'battlelog'], function () {
        Route::group(['prefix' => 'players'], function () {
            Route::get('{player}/weapons',
                ['as' => 'api.battlelog.players.weapons', 'uses' => 'BattlelogController@getWeapons'])->where('player',
                '[0-9]+');

            Route::get('{player}/overview', [
                'as'   => 'api.battlelog.players.overview',
                'uses' => 'BattlelogController@getOverview',
            ])->where('player', '[0-9]+');

            Route::get('{player}/vehicles', [
                'as'   => 'api.battlelog.players.vehicles',
                'uses' => 'BattlelogController@getVehicles',
            ])->where('player', '[0-9]+');

            Route::get('{player}/reports',
                ['as' => 'api.battlelog.players.reports', 'uses' => 'BattlelogController@getReports'])->where('player',
                '[0-9]+');

            Route::get('{player}/report/{id}',
                ['as' => 'api.battlelog.players.report', 'uses' => 'BattlelogController@getReport'])->where('player',
                '[0-9]+')->where('id', '[0-9]+');

            Route::get('{player}/acs', [
                'as'   => 'api.battlelog.players.acs',
                'uses' => 'BattlelogController@getCheatDetection',
            ])->where('player', '[0-9]+');
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
            Route::get('assessments',
                ['as' => 'api.bans.metabans.assessments', 'uses' => 'MetabansController@getAssessments']);
            Route::get('feed_assessments',
                ['as' => 'api.bans.metabans.feed_assessments', 'uses' => 'MetabansController@getFeedAssessments']);
        });
    });

    /*===================================
    =            API Servers            =
    ===================================*/

    Route::group(['prefix' => 'servers'], function () {
        Route::get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
        Route::get('scoreboard/{id}',
            ['as' => 'api.servers.scoreboard', 'uses' => 'ServersController@scoreboard'])->where('id', '[0-9]+');
        Route::get('scoreboard/roundstats/{id}',
            ['as' => 'api.servers.scoreboard.roundstats', 'uses' => 'ServersController@scoreboardExtra'])->where('id',
            '[0-9]+');
        Route::get('chat/{id}', ['as' => 'api.servers.chat', 'uses' => 'ServersController@chat'])->where('id',
            '[0-9]+');
        Route::controller('admin/scoreboard', 'Admin\ScoreboardController');
    });

    /*====================================
    =            API Chatlogs            =
    ====================================*/

    Route::group(['prefix' => 'chatlogs'], function () {
        Route::get('/', ['as' => 'api.chatlogs.index', 'uses' => 'ChatlogController@getIndex']);
    });

    /*===================================
    =            API Reports            =
    ===================================*/
    Route::controller('reports', 'ReportsController');
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
    Route::get('register',
        ['as' => 'user.register', 'uses' => 'UsersController@showSignup', 'before' => 'guest|user.register.enabled']);
    Route::get('user/confirm/{code}',
        ['as' => 'user.confirm', 'uses' => 'UsersController@confirm', 'before' => 'guest']);
    Route::post('login', ['as' => 'user.login.post', 'uses' => 'UsersController@login', 'before' => 'guest|csrf']);
    Route::post('register',
        ['as' => 'user.register.post', 'uses' => 'UsersController@signup', 'before' => 'guest|csrf']);

    /*-----  End of Auth Routes  ------*/

    /*===================================
    =            User Router            =
    ===================================*/

    Route::get('account',
        ['as' => 'user.account', 'uses' => 'UsersController@showAccountSettings', 'before' => 'auth']);
    Route::put('account',
        ['as' => 'user.account.save', 'uses' => 'UsersController@saveAccountSettings', 'before' => 'auth']);

    /*=====  End of User Router  ======*/


    Route::get('chatlogs', ['as' => 'chatlog.search', 'uses' => 'ChatlogController@index', 'before' => 'chatlogs']);

    Route::group(['prefix' => 'players'], function () {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
        Route::get('{id}/{name?}', ['as' => 'player.show', 'uses' => 'PlayersController@profile'])->where('id',
            '[0-9]+');
    });

    Route::group(['prefix' => 'servers'], function () {
        Route::get('live', ['as' => 'servers.live', 'uses' => 'HomeController@scoreboard']);
        Route::get('list', ['as' => 'servers.list', 'uses' => 'ServersController@index']);
        Route::get('show/{id}/{name?}', ['as' => 'servers.show', 'uses' => 'ServersController@show']);
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::group(['prefix' => 'adkats', 'namespace' => 'AdKats'], function () {

            // Adkats Settings
            Route::resource('settings', 'SettingsController', [
                'names' => [
                    'index' => 'admin.adkats.settings.index',
                    'edit'  => 'admin.adkats.settings.edit',
                ],
                'only'  => ['index', 'edit'],
            ]);

            // Adkats Bans
            Route::resource('bans', 'BansController', [
                'names' => [
                    'index'   => 'admin.adkats.bans.index',
                    'edit'    => 'admin.adkats.bans.edit',
                    'update'  => 'admin.adkats.bans.update',
                    'destroy' => 'admin.adkats.bans.destroy',
                    'store'   => 'admin.adkats.bans.store',
                    'create'  => 'admin.adkats.bans.create',
                ],
                'only'  => ['index', 'edit', 'update', 'destroy', 'store', 'create'],
            ]);

            // Adkats Users
            Route::resource('users', 'UsersController', [
                'names' => [
                    'index'   => 'admin.adkats.users.index',
                    'edit'    => 'admin.adkats.users.edit',
                    'store'   => 'admin.adkats.users.store',
                    'update'  => 'admin.adkats.users.update',
                    'destroy' => 'admin.adkats.users.destroy',
                ],
                'only'  => ['index', 'edit', 'update', 'destroy', 'store'],
            ]);

            // Adkats Roles
            Route::resource('roles', 'RolesController', [
                'names' => [
                    'index'   => 'admin.adkats.roles.index',
                    'edit'    => 'admin.adkats.roles.edit',
                    'store'   => 'admin.adkats.roles.store',
                    'update'  => 'admin.adkats.roles.update',
                    'create'  => 'admin.adkats.roles.create',
                    'destroy' => 'admin.adkats.roles.destroy',
                ],
            ]);

            Route::resource('special_players', 'SpecialPlayersController', [
                'names' => [
                    'index'  => 'admin.adkats.special_players.index',
                    'update' => 'admin.adkats.special_players.update',
                ],
                'only'  => ['index', 'update'],
            ]);

            Route::resource('reports', 'ReportsController', [
                'names' => [
                    'index' => 'admin.adkats.reports.index',
                ],
                'only'  => ['index'],
            ]);
        });

        Route::group(['prefix' => 'site', 'namespace' => 'Site'], function () {
            Route::resource('users', 'UsersController', [
                'names' => [
                    'index'   => 'admin.site.users.index',
                    'edit'    => 'admin.site.users.edit',
                    'destroy' => 'admin.site.users.destroy',
                    'update'  => 'admin.site.users.update',
                    'create'  => 'admin.site.users.create',
                    'store'   => 'admin.site.users.store',
                ],
            ]);

            Route::resource('roles', 'RolesController', [
                'names' => [
                    'index'   => 'admin.site.roles.index',
                    'edit'    => 'admin.site.roles.edit',
                    'destroy' => 'admin.site.roles.destroy',
                    'update'  => 'admin.site.roles.update',
                    'create'  => 'admin.site.roles.create',
                    'store'   => 'admin.site.roles.store',
                ],
            ]);

            Route::resource('servers', 'ServersController', [
                'names' => [
                    'index'  => 'admin.site.servers.index',
                    'edit'   => 'admin.site.servers.edit',
                    'update' => 'admin.site.servers.update',
                ],
                'only'  => ['index', 'edit', 'update'],
            ]);

            Route::get('settings', ['as' => 'admin.site.settings.index', 'uses' => 'SettingsController@index']);
            Route::put('settings', ['as' => 'admin.site.settings.update', 'uses' => 'SettingsController@update']);

            Route::get('system/maintenance', [
                'as'     => 'admin.site.maintenance.index',
                'uses'   => 'MaintenanceController@index',
                'before' => 'auth|ip.whitelisted',
            ]);
            Route::post('system/maintenance', [
                'as'     => 'admin.site.maintenance.update',
                'uses'   => 'MaintenanceController@update',
                'before' => 'auth|ip.whitelisted',
            ]);
        });

        Route::get('updater', ['as' => 'admin.updater.index', 'uses' => 'UpdaterController@index']);
    });
});

/**
 * Route Permissions
 */

/*===================================
=            Adkats Bans            =
===================================*/
Entrust::routeNeedsPermission('admin/adkats/bans', 'admin.adkats.bans.view');
Entrust::routeNeedsPermission('admin/adkats/bans/create', 'admin.adkats.bans.create');
Entrust::routeNeedsPermission('admin/adkats/bans/*', 'admin.adkats.bans.edit');

/*====================================
=            Adkats Users            =
====================================*/
Entrust::routeNeedsPermission('admin/adkats/users', 'admin.adkats.user.view');
Entrust::routeNeedsPermission('admin/adkats/users/*', 'admin.adkats.user.edit');

/*====================================
=            Adkats Roles            =
====================================*/
Entrust::routeNeedsPermission('admin/adkats/roles', 'admin.adkats.roles.view');
Entrust::routeNeedsPermission('admin/adkats/roles/*', 'admin.adkats.roles.edit');

/*============================================
=            Adkats Special Users            =
============================================*/
Entrust::routeNeedsPermission('admin/adkats/special_players', 'admin.adkats.special.view');
Entrust::routeNeedsPermission('admin/adkats/special_players/*', 'admin.adkats.special.edit');

/*=======================================
=            Adkats Settings            =
=======================================*/
Entrust::routeNeedsPermission('admin/adkats/settings', 'admin.adkats.settings.edit');
Entrust::routeNeedsPermission('admin/adkats/settings/*', 'admin.adkats.settings.edit');

/*==================================
=            Site Users            =
==================================*/
Entrust::routeNeedsPermission('admin/site/users', 'admin.site.users');
Entrust::routeNeedsPermission('admin/site/users/*', 'admin.site.users');

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

/*=====================================
=            Site Settings            =
=====================================*/
Entrust::routeNeedsPermission('admin/site/settings', 'admin.site.settings.site');
Entrust::routeNeedsPermission('admin/updater', 'admin.site.settings.site');

/*============================================
=            Site Server Settings            =
============================================*/
Entrust::routeNeedsPermission('admin/site/servers', 'admin.site.settings.server');
Entrust::routeNeedsPermission('admin/site/servers/*', 'admin.site.settings.server');

/*========================================
=            Site System Logs            =
========================================*/
Entrust::routeNeedsPermission(Config::get('logviewer::base_url'), 'admin.site.system.logs');
Entrust::routeNeedsPermission(Config::get('logviewer::base_url') . '/*', 'admin.site.system.logs');

/*================================================
=            Require the Menu Builder            =
================================================*/

if (!file_exists($app['path.base'] . '/app/bfacp/setup.php')) {
    require $app['path.base'] . '/app/menu.php';
}
