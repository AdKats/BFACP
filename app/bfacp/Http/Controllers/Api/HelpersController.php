<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class HelpersController extends BaseController
{
    public function getSpecialGroups()
    {
        $groups = MainHelper::specialGroups();

        return MainHelper::response($groups, null, null, null, false, true);
    }

    public function onlineAdmins()
    {
        $admins = DB::table('tbl_currentplayers')->select('SoldierName', 'ServerName',
            'tbl_currentplayers.ServerID', 'PlayerJoined')->join('tbl_server',
            'tbl_currentplayers.ServerID', '=', 'tbl_server.ServerID')
            ->whereIn('EA_GUID', function ($query) {
                $query->from('adkats_usersoldiers')->select('EAGUID')->join('adkats_users',
                    'adkats_usersoldiers.user_id', '=',
                    'adkats_users.user_id')->join('adkats_roles', 'adkats_users.user_role', '=',
                    'adkats_roles.role_id')->join('tbl_playerdata', 'adkats_usersoldiers.player_id', '=',
                    'tbl_playerdata.PlayerID')->groupBy('EAGUID')->whereExists(function ($query2) {
                    $query2->select('adkats_rolecommands.role_id')->from('adkats_rolecommands')->join('adkats_commands',
                        'adkats_rolecommands.command_id', '=',
                        'adkats_commands.command_id')->where('command_playerInteraction',
                        1)->whereRaw('adkats_rolecommands.role_id = adkats_users.user_role')
                        ->groupBy('adkats_rolecommands.role_id');
                });
            })->get();

        foreach ($admins as $key => $admin) {
            $admins[ $key ]->stamp = Carbon::parse($admin->PlayerJoined, 'UTC')->toIso8601String();
        }

        return MainHelper::response($admins, null, null, null, false, true);
    }
}
