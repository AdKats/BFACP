<?php

/**
 * Route API Registering.
 */

use Illuminate\Support\Facades\Route;

if (PHP_SAPI !== 'cli') {
    Route::group(['namespace' => 'Api', 'middleware' => ['web', 'throttle:100,1'], 'prefix' => 'api'], function () {
        /*===================================
        =            API Resources          =
        ===================================*/
        Route::group(['prefix' => 'helpers'], function () {
            Route::group(['prefix' => 'adkats'], function () {
                Route::get('special_groups', 'HelpersController@getSpecialGroups');
            });
            Route::get('online/admins', 'HelpersController@onlineAdmins');
            Route::get('ip/{addy}', 'HelpersController@iplookup');
            Route::get('squads', 'HelpersController@getSquads');
        });

        /*===================================
        =            API Pusher             =
        ===================================*/

        Route::controller('pusher', 'PusherController');

        /*===================================
        =            API Players            =
        ===================================*/

        Route::group(['prefix' => 'players'], function ($api) {
            Route::get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
            Route::get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])->where('id', '[0-9]+');
            Route::get('{id}/records',
                ['as' => 'api.players.show.records', 'uses' => 'PlayersController@showRecords'])->where('id', '[0-9]+');
            Route::get('{id}/chatlogs',
                ['as' => 'api.players.show.chatlogs', 'uses' => 'PlayersController@showChatlogs'])->where('id',
                '[0-9]+');
            Route::get('{id}/sessions',
                ['as' => 'api.players.show.sessions', 'uses' => 'PlayersController@showSessions'])->where('id',
                '[0-9]+');
        });

        /*=====================================
        =            API Battlelog            =
        =====================================*/

        Route::group(['prefix' => 'battlelog'], function ($api) {
            Route::group(['prefix' => 'players'], function ($api) {
                Route::get('{player}/weapons', [
                    'as' => 'api.battlelog.players.weapons',
                    'uses' => 'BattlelogController@getWeapons',
                ])->where('player', '[0-9]+');

                Route::get('{player}/overview', [
                    'as' => 'api.battlelog.players.overview',
                    'uses' => 'BattlelogController@getOverview',
                ])->where('player', '[0-9]+');

                Route::get('{player}/vehicles', [
                    'as' => 'api.battlelog.players.vehicles',
                    'uses' => 'BattlelogController@getVehicles',
                ])->where('player', '[0-9]+');

                Route::get('{player}/reports', [
                    'as' => 'api.battlelog.players.reports',
                    'uses' => 'BattlelogController@getReports',
                ])->where('player', '[0-9]+');

                Route::get('{player}/report/{id}', [
                    'as' => 'api.battlelog.players.report',
                    'uses' => 'BattlelogController@getReport',
                ])->where('player', '[0-9]+')->where('id', '[0-9]+');

                Route::get('{player}/acs', [
                    'as' => 'api.battlelog.players.acs',
                    'uses' => 'BattlelogController@getCheatDetection',
                ])->where('player', '[0-9]+');
            });
        });

        /*================================
        =            API Bans            =
        ================================*/

        Route::group(['prefix' => 'bans'], function ($api) {
            Route::get('latest', ['as' => 'api.bans.latest', 'uses' => 'BansController@latest']);
            Route::get('stats', ['as' => 'api.bans.stats', 'uses' => 'BansController@stats']);

            Route::group(['prefix' => 'metabans'], function ($api) {
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

        Route::group(['prefix' => 'servers'], function ($api) {
            Route::get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
            Route::get('scoreboard/{id}',
                ['as' => 'api.servers.scoreboard', 'uses' => 'ServersController@scoreboard'])->where('id', '[0-9]+');
            Route::get('scoreboard/roundstats/{id}', [
                'as' => 'api.servers.scoreboard.roundstats',
                'uses' => 'ServersController@scoreboardExtra',
            ])->where('id', '[0-9]+');
            Route::get('chat/{id}', ['as' => 'api.servers.chat', 'uses' => 'ServersController@chat'])->where('id',
                '[0-9]+');
            Route::get('extras/{server}', ['as' => 'api.servers.extras', 'uses' => 'ServersController@extras']);
            Route::controller('admin/scoreboard', 'Admin\ScoreboardController');
        });

        /*====================================
        =            API Chatlogs            =
        ====================================*/

        Route::group(['prefix' => 'chatlogs'], function ($api) {
            Route::get('/', ['as' => 'api.chatlogs.index', 'uses' => 'ChatlogController@getIndex']);
        });

        /*===================================
        =            API Reports            =
        ===================================*/
        Route::controller('reports', 'ReportsController');
    });
}

/*
 * Route Application Registering
 */
Route::group(['middleware' => 'web'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    /*===================================
    =            Auth Routes            =
    ===================================*/

    Route::get('login', ['as' => 'user.login', 'uses' => 'Auth\AuthController@showLoginForm']);
    Route::get('logout', ['as' => 'user.logout', 'uses' => 'Auth\AuthController@logout']);
    Route::get('register', ['as' => 'user.register', 'uses' => 'Auth\AuthController@showRegistrationForm']);
    Route::post('login', ['as' => 'user.login.post', 'uses' => 'Auth\AuthController@login']);
    Route::post('register', ['as' => 'user.register.post', 'uses' => 'Auth\AuthController@register']);
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\PasswordController@reset');

    /*-----  End of Auth Routes  ------*/

    /*===================================
    =            User Router            =
    ===================================*/

    Route::get('account',
        ['as' => 'user.account', 'uses' => 'UsersController@showAccountSettings', 'middleware' => 'auth']);
    Route::put('account',
        ['as' => 'user.account.save', 'uses' => 'UsersController@saveAccountSettings', 'middleware' => 'auth']);
    Route::get('profile/{id}-{name}', [
        'as'         => 'user.profile',
        'uses'       => 'UsersController@showProfile',
        'middleware' => 'auth',
    ])->where('id', '[0-9]+');

    /*=====  End of User Router  ======*/

    Route::get('chatlogs', ['as' => 'chatlog.search', 'uses' => 'ChatlogController@index', 'middleware' => 'chatlogs']);

    Route::group(['prefix' => 'players'], function () {
        Route::get('/', ['as' => 'player.listing', 'uses' => 'PlayersController@listing']);
        Route::post('{player}/forgive', [
            'as'         => 'player.update',
            'uses' => 'PlayersController@issueForgive',
            'middleware' => 'auth',
        ])->where('id', '[0-9]+');
        Route::get('{id}/{name?}', [
            'as'   => 'player.show',
            'uses' => 'PlayersController@profile',
        ])->where('id', '[0-9]+');
    });

    Route::get('rep', ['as' => 'rep.listing', 'uses' => 'ReputationController@index']);

    Route::group(['prefix' => 'servers'], function () {
        Route::get('live', ['as' => 'servers.live', 'uses' => 'ServersController@scoreboard']);
        Route::get('list', ['as' => 'servers.list', 'uses' => 'ServersController@index']);
        Route::get('show/{server}/{slug?}', ['as' => 'servers.show', 'uses' => 'ServersController@show']);
        Route::controller('admin/scoreboard', 'Api\Admin\ScoreboardController');
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::group(['prefix' => 'adkats', 'namespace' => 'Adkats'], function () {

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

            // Adkats Roles
            Route::resource('infractions', 'InfractionsController', [
                'names' => [
                    'index' => 'admin.adkats.infractions.index',
                    // 'edit'    => 'admin.adkats.infractions.edit',
                    // 'store'   => 'admin.adkats.infractions.store',
                    // 'update'  => 'admin.adkats.infractions.update',
                    // 'create'  => 'admin.adkats.infractions.create',
                    // 'destroy' => 'admin.adkats.infractions.destroy',
                ],
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

            Route::resource('server', 'ServersController', [
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
                'as'         => 'admin.site.maintenance.index',
                'uses'       => 'MaintenanceController@index',
                'middleware' => ['auth', 'whitelisted'],
            ]);

            Route::post('system/maintenance', [
                'as'         => 'admin.site.maintenance.update',
                'uses'       => 'MaintenanceController@update',
                'middleware' => ['auth', 'whitelisted'],
            ]);
        });

        Route::get('updater', ['as' => 'admin.updater.index', 'uses' => 'UpdaterController@index']);
    });
});
