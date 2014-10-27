<?php

Route::get('legal/copyright', array('as' => 'copyrightNotice', function()
{
    return View::make('copyright')->with('title', 'Copyright Policy');
}));

Route::get('{game}/playerinfo/{id}', function($game, $id)
{
    $player = ADKGamers\Webadmin\Models\Battlefield\Player::find($id);
    return Redirect::action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', [$player->PlayerID, $player->SoldierName], 301);
})->where('id', '[0-9]+');

/**
 * Auth Routes
 */
Route::group(array('before' => 'auth'), function()
{
    Route::get('account', 'ADKGamers\\Webadmin\\Controllers\\AccountController@showAccountSettings');
    Route::post('account', 'ADKGamers\\Webadmin\\Controllers\\AccountController@updateSettings');
    Route::get('logout', 'ADKGamers\\Webadmin\\Controllers\\UserController@logout');
});

Route::group(array('before' => 'guest'), function()
{
    Route::get('/login', 'ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn');
    Route::get('/signup', 'ADKGamers\\Webadmin\\Controllers\\UserController@showSignUp');
    Route::get('/forgot_password', 'ADKGamers\\Webadmin\\Controllers\\UserController@showForgotPassword');
    Route::get('/confirm_account/{code}', 'ADKGamers\\Webadmin\\Controllers\\UserController@confirm');
    Route::get('/reset_password/{token}', 'ADKGamers\\Webadmin\\Controllers\\UserController@showResetPassword');
    Route::post('/login', array('uses' => 'ADKGamers\\Webadmin\\Controllers\\UserController@do_login', 'before' => 'csrf'));
    Route::post('/signup', array('uses' => 'ADKGamers\\Webadmin\\Controllers\\UserController@store', 'before' => 'csrf'));
    Route::post('/forgot_password', array('uses' => 'ADKGamers\\Webadmin\\Controllers\\UserController@do_forgot_password', 'before' => 'csrf'));
    Route::post('/reset_password', array('uses' => 'ADKGamers\\Webadmin\\Controllers\\UserController@do_reset_password', 'before' => 'csrf'));
});

Route::get('/profile/{id}', 'ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile')->where('id', '[0-9]+');

/**
 * API Routes
 */
Route::group(array('prefix' => 'api/v1'), function()
{
    Route::any('/', function() { return Helper::response('error', 'Invalid Request'); });
    Route::controller('bf3', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\Battlefield3\\Routing');
    Route::controller('bf4', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\Battlefield4\\Routing');
    Route::controller('admin/server', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\Battlefield\\Admin\\AdminServerDirect');
    Route::group(array('prefix' => 'common'), function()
    {
        Route::any('metabans_feed', function()
        {
            $m = new ADKGamers\Webadmin\Libs\Metabans;
            return $m->feed();
        });

        Route::controller('general', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\Battlefield\\Common\\General');
    });

    Route::resource('uptime', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\UptimeRobot');

    // TODO
    // Route::resource('acp/adkats/special_playerlist', 'ADKGamers\\Webadmin\\Controllers\\Api\\v1\\Battlefield\\Admin\\AdKats\\SpecialPlayersController');
});

/**
 * Application routes
 */
Route::get('/', 'ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex');
Route::get('search', 'ADKGamers\\Webadmin\\Controllers\\PublicController@searchForPlayer');
Route::get('scoreboard', function()
{
    $data['bf3'] = ADKGamers\Webadmin\Models\Battlefield\Server::bf3()->get();
    $data['bf4'] = ADKGamers\Webadmin\Models\Battlefield\Server::bf4()->get();
    return View::make('public.battlefield.common.scoreboard')->with('title', 'Live Scoreboard')->with('servers', $data);
});

Route::get('chatlogs', 'ADKGamers\\Webadmin\\Controllers\\ChatlogController@showChatSearch');

Route::group(array('prefix' => 'player'), function()
{
    Route::get('{id?}/{name?}', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo');
    Route::post('{id}/extended/history', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@chartHistory');
    Route::post('{id}/extended/chatlog', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@getChatLog');
    Route::post('{id}/extended/records', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfoRecords');
    Route::post('{id}/extended/stats', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@getPlayerStats');
    Route::post('{id}/extended/rep', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@getRep');
    Route::post('{id}/extended/sites', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@externalRequests');
    Route::post('{id}/forgive', 'ADKGamers\\Webadmin\\Controllers\\PlayerController@forgive');
});

Route::get('stats', 'ADKGamers\\Webadmin\\Controllers\\PublicController@showServerStats');

Route::group(array('prefix' => 'leaderboard'), function()
{
    Route::get('reputation', 'ADKGamers\\Webadmin\\Controllers\\PublicController@showLeaderboardReputation');
    Route::get('playerstats', 'ADKGamers\\Webadmin\\Controllers\\PublicController@showLeaderboardPlayers');
});

Route::get('memberlist', 'ADKGamers\\Webadmin\\Controllers\\PublicController@showMemberlist');

Route::get('rss/bans/{game}', 'ADKGamers\\Webadmin\\Controllers\\PublicController@rssBans');

/**
 * AdKats Administration
 */
Route::group(array('prefix' => 'acp/adkats', 'before' => 'auth'), function()
{
    Route::get('user/{id}/confirm_delete', array('as' => 'adkat_acc_del_confirm', function($id = NULL)
    {
        if(is_null($id))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@index');
        }

        $user = ADKGamers\Webadmin\Models\AdKats\User::find($id);

        return View::make('admin.adkats.users.confirmdelete')->with('user', $user)->with('title', 'Confirm Account Deletion');
    }));

    Route::resource('user', 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController');

    Route::get('role/{id}/confirm_delete', array('as' => 'adkat_role_del_confirm', function($id = NULL)
    {
        if(is_null($id))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index');
        }

        $role = ADKGamers\Webadmin\Models\AdKats\Role::find($id);

        return View::make('admin.adkats.role_perms.confirmdelete')->with('role', $role)->with('title', 'Confirm Role Deletion');
    }));

    Route::resource('role', 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController');

    Route::resource('ban', 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController');
    Route::resource('plugin', 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\PluginController');

    // TODO
    // Route::resource('special_playerlist', 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\SpecialPlayersController');
});

/**
 * BFAdminCP Administration
 */
Route::group(array('prefix' => 'acp/site', 'before' => 'auth'), function()
{
    Route::get('user/{id}/confirm_delete', array('as' => 'site_acc_del_confirm', function($id = NULL)
    {
        if(is_null($id))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@index');
        }

        $user = User::find($id);

        return View::make('admin.users.confirmdelete')->with('user', $user)->with('title', 'Confirm Account Deletion');
    }));

    Route::resource('user', 'ADKGamers\\Webadmin\\Controllers\\Admin\\UserController');

    Route::get('role/{id}/confirm_delete', array('as' => 'site_role_del_confirm', function($id = NULL)
    {
        if(is_null($id))
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index');
        }

        $role = Role::find($id);

        return View::make('admin.role_perms.confirmdelete')->with('role', $role)->with('title', 'Confirm Role Deletion');
    }));

    Route::resource('role', 'ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController');

    Route::resource('setting', 'ADKGamers\\Webadmin\\Controllers\\Admin\\SiteController');
    Route::resource('gameserver', 'ADKGamers\\Webadmin\\Controllers\\Admin\\SiteGameServerController');

    Route::group(array('prefix' => 'info'), function()
    {
        Route::get('database', function()
        {
            $dbsizetotal = DB::select(File::get(storage_path() . '/sql/database_size_total.sql'));
            $dbsizetables = DB::select(File::get(storage_path() . '/sql/database_size_tables.sql'));

            $pie = [];

            foreach($dbsizetables as $table)
            {
                $pie[] = [
                    $table->tables,
                    floatval($table->size_in_mb)
                ];
            }

            return View::make('admin.information.database')
                    ->with('title', 'Database Stats')
                    ->with('piedata', json_encode($pie))
                    ->with('dbtotal', $dbsizetotal)
                    ->with('dbtables', $dbsizetables);
        });
    });
});

/*
Route::get('test', function()
{
    $settings = ADKGamers\Webadmin\Models\AdKats\Setting::where('server_id', 1)->get();

    $new_settings = [];

    foreach($settings as $setting)
    {
        $search  = array( " ", "-", "/", ":", "(", ")" );
        $replace = array( "_", "_", "_", "" );
        $keyName = str_replace( $search, $replace, trim( strtolower( $setting['setting_name'] ) ) );

        switch($setting['setting_type'])
        {
            case "double":
                $new_settings['numeric'][$keyName] = [
                    'display_name' => $setting['setting_name'],
                    'value' => floatval($setting['setting_value'])
                ];
            break;

            case "int":
                $new_settings['numeric'][$keyName] = [
                    'display_name' => $setting['setting_name'],
                    'value' => intval($setting['setting_value'])
                ];
            break;

            case "bool":
                $new_settings['boolean'][$keyName] = [
                    'display_name' => $setting['setting_name'],
                    'value' => $setting['setting_value'] == 'True' ? TRUE : FALSE
                ];
            break;

            case "multiline":
                $new_settings['strings'][$keyName] = [
                    'display_name' => $setting['setting_name'],
                    'value' => (
                        $setting['setting_name'] != 'Custom HTML Addition' && str_contains($setting['setting_value'], "|") ?
                        explode( '|', urldecode( rawurldecode( $setting['setting_value'] ) ) ) :
                        trim($setting['setting_value'])
                    )
                ];
            break;
        }
    }

    return Helper::response('success', NULL, $new_settings);
});
*/

