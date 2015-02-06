<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Battlefield\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use MainHelper;

class ServersController extends BaseController
{
    public function population()
    {
        $servers = Server::active()->get();

        $usedSlots = $servers->sum('usedSlots');
        $totalSlots = $servers->sum('maxSlots');
        $newCollection = [];

        foreach($servers as $server)
        {
            $gameKey = strtolower($server->game->Name);
            $newCollection[$gameKey]['servers'][] = $server;
        }

        foreach($newCollection as $key => $collection)
        {
            $online = 0;
            $total = 0;

            foreach($collection['servers'] as $server)
            {
                $online += $server->usedSlots;
                $total += $server->maxSlots;
            }

            $newCollection[$key]['stats'] = [
                'online' => $online,
                'totalSlots' => $total,
                'percentage' => MainHelper::percent($online, $total)
            ];
        }

        return MainHelper::response([
            'online'     => $usedSlots,
            'totalSlots' => $totalSlots,
            'percentage' => MainHelper::percent($usedSlots, $totalSlots),
            'games'      => $newCollection
        ] + Lang::get('dashboard.population'), NULL, NULL, NULL, FALSE, TRUE);
    }
}
