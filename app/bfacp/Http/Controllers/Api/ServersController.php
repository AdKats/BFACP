<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Server;
use BFACP\Exceptions\RconException;
use BFACP\Repositories\Scoreboard\DBRepository AS SBDBRepo;
use BFACP\Repositories\Scoreboard\LiveServerRepository AS SBLiveRepo;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use MainHelper;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

class ServersController extends BaseController
{
    /**
     * Gathers the population for all servers
     * @return array
     */
    public function population()
    {
        // Get active servers only
        $servers = Server::active()->get();

        // Sum the used slots
        $usedSlots = $servers->sum('usedSlots');

        // Sum the max slots
        $totalSlots = $servers->sum('maxSlots');

        // Init array
        $newCollection = [];

        foreach($servers as $server)
        {
            // Convert the game name to lowercase
            $gameKey = strtolower($server->game->Name);

            // Add the server to the collection
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

    public function scoreboard($id)
    {
        try
        {
            $server = Server::remember(10)->findOrFail($id);
            $scoreboard = new SBLiveRepo($server);

            if( $scoreboard->attempt()->check() )
            {
                return MainHelper::response($scoreboard->get(), NULL, NULL, NULL, FALSE, TRUE);
            }
        }
        catch(RconException $e)
        {
            throw $e;
        }
        catch(\Exception $e)
        {
            throw $e;
        }

        return MainHelper::response(NULL, 'Could not load server', 'error', NULL, FALSE, TRUE);
    }

    public function chat($id)
    {
        $chat = Chat::with('player')->where('ServerID', $id);

        if(Input::has('nospam') && Input::get('nospam') == 1)
        {
            $chat = $chat->excludeSpam();
        }

        if(Input::has('sb') && Input::get('sb') == 1)
        {
            $chat = $chat->orderBy('logDate', 'desc')->take(100)->get();
        }
        else
        {
            $chat = $chat->simplePaginate(30);
        }

        return MainHelper::response($chat, NULL, NULL, NULL, FALSE, TRUE);
    }

    public function scoreboardExtra($id)
    {
        $sql = File::get(storage_path() . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'sbRoundStats.sql');

        $stats = [
            [
                'name' => Lang::get('scoreboard.factions')[1]['full_name'] . ' - Tickets',
                'data' => [],
                'visible' => true
            ],
            [
                'name' => Lang::get('scoreboard.factions')[2]['full_name'] . ' - Tickets',
                'data' => [],
                'visible' => true
            ],
            [
                'name' => Lang::get('scoreboard.factions')[1]['full_name'] . ' - Players',
                'data' => [],
                'visible' => false
            ],
            [
                'name' => Lang::get('scoreboard.factions')[2]['full_name'] . ' - Players',
                'data' => [],
                'visible' => false
            ],
            [
                'name' => 'Players Online',
                'data' => [],
                'visible' => false
            ]
        ];

        $data['roundId'] = null;

        $results = DB::select($sql, [$id]);

        foreach($results as $result)
        {
            if( is_null($data['roundId']) )
            {
                $data['roundId'] = $result->round_id;
            }

            $stats[0]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team1_tickets
            ];

            $stats[1]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team2_tickets
            ];

            $stats[2]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team1_count
            ];

            $stats[3]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team2_count
            ];

            $stats[4]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) ($result->team1_count + $result->team2_count)
            ];
        }

        $data['stats'] = $stats;

        return MainHelper::response($data, NULL, NULL, NULL, FALSE, TRUE);
    }
}
