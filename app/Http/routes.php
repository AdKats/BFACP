<?php

/**
 * Route API Registering
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'BFACP\Http\Controllers\Api', 'middleware' => 'web'], function ($api) {

    /*===================================
    =            API Resources          =
    ===================================*/
    $api->group(['prefix' => 'helpers'], function ($api) {
        $api->group(['prefix' => 'adkats'], function ($api) {
            $api->get('special_groups', 'HelpersController@getSpecialGroups');
        });
        $api->get('online/admins', 'HelpersController@onlineAdmins');
        $api->get('ip/{addy}', 'HelpersController@iplookup');
        $api->get('squads', function () {
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

    $api->controller('pusher', 'PusherController');

    /*===================================
    =            API Players            =
    ===================================*/

    $api->group(['prefix' => 'players'], function ($api) {
        $api->get('/', ['as' => 'api.players.index', 'uses' => 'PlayersController@index']);
        $api->get('{id}', ['as' => 'api.players.show', 'uses' => 'PlayersController@show'])->where('id', '[0-9]+');
        $api->get('{id}/records',
            ['as' => 'api.players.show.records', 'uses' => 'PlayersController@showRecords'])->where('id', '[0-9]+');
        $api->get('{id}/chatlogs',
            ['as' => 'api.players.show.chatlogs', 'uses' => 'PlayersController@showChatlogs'])->where('id', '[0-9]+');
        $api->get('{id}/sessions',
            ['as' => 'api.players.show.sessions', 'uses' => 'PlayersController@showSessions'])->where('id', '[0-9]+');
    });

    /*=====================================
    =            API Battlelog            =
    =====================================*/

    $api->group(['prefix' => 'battlelog'], function ($api) {
        $api->group(['prefix' => 'players'], function ($api) {
            $api->get('{player}/weapons',
                ['as' => 'api.battlelog.players.weapons', 'uses' => 'BattlelogController@getWeapons'])->where('player',
                '[0-9]+');

            $api->get('{player}/overview', [
                'as'   => 'api.battlelog.players.overview',
                'uses' => 'BattlelogController@getOverview',
            ])->where('player', '[0-9]+');

            $api->get('{player}/vehicles', [
                'as'   => 'api.battlelog.players.vehicles',
                'uses' => 'BattlelogController@getVehicles',
            ])->where('player', '[0-9]+');

            $api->get('{player}/reports',
                ['as' => 'api.battlelog.players.reports', 'uses' => 'BattlelogController@getReports'])->where('player',
                '[0-9]+');

            $api->get('{player}/report/{id}',
                ['as' => 'api.battlelog.players.report', 'uses' => 'BattlelogController@getReport'])->where('player',
                '[0-9]+')->where('id', '[0-9]+');

            $api->get('{player}/acs', [
                'as'   => 'api.battlelog.players.acs',
                'uses' => 'BattlelogController@getCheatDetection',
            ])->where('player', '[0-9]+');
        });
    });

    /*================================
    =            API Bans            =
    ================================*/

    $api->group(['prefix' => 'bans'], function ($api) {
        $api->get('latest', ['as' => 'api.bans.latest', 'uses' => 'BansController@latest']);
        $api->get('stats', ['as' => 'api.bans.stats', 'uses' => 'BansController@stats']);

        $api->group(['prefix' => 'metabans'], function ($api) {
            $api->get('/', ['as' => 'api.bans.metabans.index', 'uses' => 'MetabansController@getIndex']);
            $api->get('feed', ['as' => 'api.bans.metabans.feed', 'uses' => 'MetabansController@getFeed']);
            $api->get('assessments',
                ['as' => 'api.bans.metabans.assessments', 'uses' => 'MetabansController@getAssessments']);
            $api->get('feed_assessments',
                ['as' => 'api.bans.metabans.feed_assessments', 'uses' => 'MetabansController@getFeedAssessments']);
        });
    });

    /*===================================
    =            API Servers            =
    ===================================*/

    $api->group(['prefix' => 'servers'], function ($api) {
        $api->get('population', ['as' => 'api.servers.population', 'uses' => 'ServersController@population']);
        $api->get('scoreboard/{id}',
            ['as' => 'api.servers.scoreboard', 'uses' => 'ServersController@scoreboard'])->where('id', '[0-9]+');
        $api->get('scoreboard/roundstats/{id}',
            ['as' => 'api.servers.scoreboard.roundstats', 'uses' => 'ServersController@scoreboardExtra'])->where('id',
            '[0-9]+');
        $api->get('chat/{id}', ['as' => 'api.servers.chat', 'uses' => 'ServersController@chat'])->where('id',
            '[0-9]+');
        $api->controller('admin/scoreboard', 'Admin\ScoreboardController');
    });

    /*====================================
    =            API Chatlogs            =
    ====================================*/

    $api->group(['prefix' => 'chatlogs'], function ($api) {
        $api->get('/', ['as' => 'api.chatlogs.index', 'uses' => 'ChatlogController@getIndex']);
    });

    /*===================================
    =            API Reports            =
    ===================================*/
    $api->controller('reports', 'ReportsController');
});

/**
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
//Entrust::routeNeedsPermission('admin/adkats/bans', 'admin.adkats.bans.view');
//Entrust::routeNeedsPermission('admin/adkats/bans/create', 'admin.adkats.bans.create');
//Entrust::routeNeedsPermission('admin/adkats/bans/*', 'admin.adkats.bans.edit');
//
///*====================================
//=            Adkats Users            =
//====================================*/
//Entrust::routeNeedsPermission('admin/adkats/users', 'admin.adkats.user.view');
//Entrust::routeNeedsPermission('admin/adkats/users/*', 'admin.adkats.user.edit');
//
///*====================================
//=            Adkats Roles            =
//====================================*/
//Entrust::routeNeedsPermission('admin/adkats/roles', 'admin.adkats.roles.view');
//Entrust::routeNeedsPermission('admin/adkats/roles/*', 'admin.adkats.roles.edit');
//
///*============================================
//=            Adkats Special Users            =
//============================================*/
//Entrust::routeNeedsPermission('admin/adkats/special_players', 'admin.adkats.special.view');
//Entrust::routeNeedsPermission('admin/adkats/special_players/*', 'admin.adkats.special.edit');
//
///*=======================================
//=            Adkats Settings            =
//=======================================*/
//Entrust::routeNeedsPermission('admin/adkats/settings', 'admin.adkats.settings.edit');
//Entrust::routeNeedsPermission('admin/adkats/settings/*', 'admin.adkats.settings.edit');
//
///*==================================
//=            Site Users            =
//==================================*/
//Entrust::routeNeedsPermission('admin/site/users', 'admin.site.users');
//Entrust::routeNeedsPermission('admin/site/users/*', 'admin.site.users');
//
///*==================================
//=            Site Users            =
//==================================*/
//Entrust::routeNeedsPermission('admin/site/users', 'admin.site.users');
//Entrust::routeNeedsPermission('admin/site/users/*', 'admin.site.users');
//
///*==================================
//=            Site Roles            =
//==================================*/
//Entrust::routeNeedsPermission('admin/site/roles', 'admin.site.roles');
//Entrust::routeNeedsPermission('admin/site/roles/*', 'admin.site.roles');
//
///*=====================================
//=            Site Settings            =
//=====================================*/
//Entrust::routeNeedsPermission('admin/site/settings', 'admin.site.settings.site');
//Entrust::routeNeedsPermission('admin/updater', 'admin.site.settings.site');
//
///*============================================
//=            Site Server Settings            =
//============================================*/
//Entrust::routeNeedsPermission('admin/site/servers', 'admin.site.settings.server');
//Entrust::routeNeedsPermission('admin/site/servers/*', 'admin.site.settings.server');
//
///*========================================
//=            Site System Logs            =
//========================================*/
//Entrust::routeNeedsPermission(Config::get('logviewer::base_url'), 'admin.site.system.logs');
//Entrust::routeNeedsPermission(Config::get('logviewer::base_url') . '/*', 'admin.site.system.logs');

