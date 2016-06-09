<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Battlefield as BattlefieldHelper;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class HelpersController.
 */
class HelpersController extends Controller
{
    /**
     * @return mixed
     */
    public function getSpecialGroups()
    {
        $groups = MainHelper::specialGroups();

        return MainHelper::response($groups, null, null, null, false, true);
    }

    /**
     * @return mixed
     */
    public function onlineAdmins()
    {
        $admins = DB::table('tbl_currentplayers')->select('SoldierName', 'ServerName', 'tbl_currentplayers.ServerID',
            'PlayerJoined')->join('tbl_server', 'tbl_currentplayers.ServerID', '=',
            'tbl_server.ServerID')->whereIn('EA_GUID', function ($query) {
                $query->from('adkats_usersoldiers')->select('EAGUID')->join('adkats_users', 'adkats_usersoldiers.user_id',
                '=', 'adkats_users.user_id')->join('adkats_roles', 'adkats_users.user_role', '=',
                'adkats_roles.role_id')->join('tbl_playerdata', 'adkats_usersoldiers.player_id', '=',
                'tbl_playerdata.PlayerID')->groupBy('EAGUID')->whereExists(function ($query2) {
                    $query2->select('adkats_rolecommands.role_id')->from('adkats_rolecommands')->join('adkats_commands',
                    'adkats_rolecommands.command_id', '=',
                    'adkats_commands.command_id')->where('command_playerInteraction',
                    1)->whereRaw('adkats_rolecommands.role_id = adkats_users.user_role')->groupBy('adkats_rolecommands.role_id');
                });
            })->get();

        foreach ($admins as $key => $admin) {
            $admins[$key]->stamp = Carbon::parse($admin->PlayerJoined, 'UTC')->toIso8601String();
        }

        return MainHelper::response($admins, null, null, null, false, true);
    }

    /**
     * @param $addy
     *
     * @return mixed
     */
    public function iplookup($addy)
    {
        $hash = md5($addy);
        $result = $this->cache->remember(sprintf('iplookup.%s', $hash), 24 * 60, function () use (&$addy) {
            $request = app('Guzzle')->get('http://ipinfo.io/'.$addy.'/json');

            return json_decode($request->getBody(), true);
        });

        return $result;
    }

    /**
     * Returns a list of squad names.
     *
     * @return mixed
     */
    public function getSquads()
    {
        $squads = [];

        for ($i = 0; $i <= 32; $i++) {
            $squads[] = [
                'id'   => $i,
                'name' => BattlefieldHelper::squad($i),
            ];
        }

        return $squads;
    }
}
