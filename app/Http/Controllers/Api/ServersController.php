<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Server\Server;
use BFACP\Exceptions\PlayerNotFoundException;
use BFACP\Exceptions\RconException;
use BFACP\Facades\Battlefield;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\Scoreboard\LiveServerRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ServersController.
 */
class ServersController extends Controller
{
    /**
     * Gathers the population for all servers.
     *
     * @param $id
     *
     * @return array
     */
    public function chat($id)
    {
        $chat = Chat::with('player')->where('ServerID', $id);

        if ($this->request->has('nospam') && $this->request->get('nospam') == 1) {
            $chat = $chat->excludeSpam();
        }

        if ($this->request->has('sb') && $this->request->get('sb') == 1) {
            $chat = $chat->orderBy('logDate', 'desc')->take(100)->get();
        } else {
            $chat = $chat->simplePaginate(30);
        }

        return MainHelper::response($chat, null, null, null, false, true);
    }

    /**
     * Live Scoreboard.
     *
     * @return array
     * @internal param int $id Server ID
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

        foreach ($servers as $server) {
            // Convert the game name to lowercase
            $gameKey = strtolower($server->game->Name);

            // Add the server to the collection
            $newCollection[$gameKey]['servers'][] = $server;
        }

        foreach ($newCollection as $key => $collection) {
            $online = 0;
            $total = 0;

            foreach ($collection['servers'] as $server) {
                $online += $server->usedSlots;
                $total += $server->maxSlots;
            }

            $newCollection[$key]['stats'] = [
                'online'     => $online,
                'totalSlots' => $total,
                'percentage' => MainHelper::percent($online, $total),
            ];
        }

        return MainHelper::response([
                'online'     => $usedSlots,
                'totalSlots' => $totalSlots,
                'percentage' => MainHelper::percent($usedSlots, $totalSlots),
                'games'      => $newCollection,
            ] + trans('dashboard.population'), null, null, null, false, true);
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws Exception
     */
    public function scoreboard($id)
    {
        try {
            $scoreboard = new LiveServerRepository(Server::findOrFail($id));

            if ($scoreboard->attempt()->check()) {
                if ($this->config->get('app.debug') && $this->request->has('verbose') && $this->request->get('verbose') == 1) {
                    $useVerbose = true;
                } else {
                    $useVerbose = false;
                }

                $data = $scoreboard->teams()->get($useVerbose);

                return MainHelper::response($data, null, null, null, false, true);
            }
        } catch (RconException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('No server found with id %s', $id));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function scoreboardExtra($id)
    {
        $stats = [
            [
                'name'    => trans('scoreboard.factions')[1]['full_name'].' - Tickets',
                'data'    => [],
                'visible' => true,
            ],
            [
                'name'    => trans('scoreboard.factions')[2]['full_name'].' - Tickets',
                'data'    => [],
                'visible' => true,
            ],
            [
                'name'    => trans('scoreboard.factions')[1]['full_name'].' - Players',
                'data'    => [],
                'visible' => false,
            ],
            [
                'name'    => trans('scoreboard.factions')[2]['full_name'].' - Players',
                'data'    => [],
                'visible' => false,
            ],
            [
                'name'    => 'Players Online',
                'data'    => [],
                'visible' => false,
            ],
        ];

        $data['roundId'] = null;

        $roundId = $this->db->table('tbl_extendedroundstats')->where('server_id', $id)->max('round_id');

        $results = $this->db->table('tbl_extendedroundstats')->where('server_id', $id)->where('round_id',
            $roundId)->get();

        foreach ($results as $result) {
            if (is_null($data['roundId'])) {
                $data['roundId'] = $result->round_id;
            }

            $stats[0]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team1_tickets,
            ];

            $stats[1]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team2_tickets,
            ];

            $stats[2]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team1_count,
            ];

            $stats[3]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) $result->team2_count,
            ];

            $stats[4]['data'][] = [
                strtotime($result->roundstat_time) * 1000,
                (int) ($result->team1_count + $result->team2_count),
            ];
        }

        $data['stats'] = $stats;

        return MainHelper::response($data, null, null, null, false, true);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function scoreboardAdmin()
    {
        $id = $this->request->get('server_id');

        try {
            if (! is_numeric($id) || $id <= 0) {
                throw new NotFoundHttpException('Invalid Server ID');
            }

            $allowedMethods = [
                'yell',
                'say',
                'kill',
                'move',
                'kick',
                'punish',
            ];

            $permissions = $this->cache->get('admin.perm.list');

            if (! $this->request->has('method') || ! in_array($this->request->get('method'), $allowedMethods)) {
                throw new NotFoundHttpException();
            }

            if (! $this->isLoggedIn || ! $this->user->ability(null, $permissions['scoreboard'])) {
                throw new AccessDeniedHttpException('Not Authorized for viewing scoreboard permissions.');
            }

            $scoreboard = new LiveServerRepository(Server::findOrFail($id));

            if ($scoreboard->attempt()->check()) {
                $players = [];

                if ($this->request->has('players')) {
                    $players = explode(',', $this->request->get('players'));
                }

                switch ($this->request->get('method')) {
                    case 'yell':
                        $this->hasPermission('admin.scoreboard.yell');

                        if ($this->request->get('type') == 'Player' && $this->request->has('players')) {
                            foreach ($players as $player) {
                                $scoreboard->adminYell($this->request->get('message', null), $player, null,
                                    $this->request->get('duration', 5), 'Player');
                            }
                        } else {
                            $scoreboard->adminYell($this->request->get('message', null),
                                $this->request->get('player', null), $this->request->get('team', null),
                                $this->request->get('duration', 5), $this->request->get('type', 'All'));
                        }
                        break;

                    case 'say':
                        $this->hasPermission('admin.scoreboard.say');

                        if ($this->request->get('type') == 'Player' && $this->request->has('players')) {
                            foreach ($players as $player) {
                                $scoreboard->adminSay($this->request->get('message', null), $player, null, 'Player');
                            }
                        } else {
                            $scoreboard->adminSay($this->request->get('message', null),
                                $this->request->get('player', null), $this->request->get('team', null),
                                $this->request->get('type', 'All'));
                        }
                        break;

                    case 'kill':
                        $this->hasPermission('admin.scoreboard.kill');

                        if ($this->request->has('players')) {
                            $unkilled = [];

                            foreach ($players as $player) {
                                try {
                                    $scoreboard->adminKill($player, $this->request->get('message', null));
                                } catch (PlayerNotFoundException $e) {
                                    $unkilled[] = [
                                        'name'   => $player,
                                        'reason' => $e->getMessage(),
                                    ];
                                }
                            }

                            if (! empty($unkilled)) {
                                $data = $unkilled;
                            }
                        } else {
                            throw new RconException(400, 'No players selected.');
                        }
                        break;

                    case 'kick':
                        $this->hasPermission('admin.scoreboard.kick');

                        if ($this->request->has('players')) {
                            $unkicked = [];

                            foreach ($players as $player) {
                                try {
                                    $scoreboard->adminKick($player, $this->request->get('message', null));
                                } catch (PlayerNotFoundException $e) {
                                    $unkicked[] = [
                                        'name'   => $player,
                                        'reason' => $e->getMessage(),
                                    ];
                                }
                            }

                            if (! empty($unkicked)) {
                                $data = $unkicked;
                            }
                        } else {
                            throw new RconException(400, 'No player selected.');
                        }
                        break;

                    case 'move':
                        $this->hasPermission('admin.scoreboard.teamswitch');

                        if ($this->request->has('players')) {
                            $unmoved = [];

                            foreach ($players as $player) {
                                try {
                                    $scoreboard->adminMovePlayer($player, $this->request->get('team', null),
                                        $this->request->get('squad', null));
                                } catch (PlayerNotFoundException $e) {
                                    $unmoved[] = [
                                        'name'   => $player,
                                        'reason' => $e->getMessage(),
                                    ];
                                } catch (RconException $e) {
                                    $unmoved[] = [
                                        'name'   => $player,
                                        'reason' => $e->getMessage(),
                                    ];
                                }
                            }

                            if (! empty($unmoved)) {
                                $data = $unmoved;
                            }
                        } else {
                            throw new RconException(400, 'No player selected.');
                        }
                        break;

                    case 'punish':
                        $this->hasPermission('admin.scoreboard.punish');

                        if ($this->request->has('players')) {
                            foreach ($players as $player) {
                                $data[] = $scoreboard->adminPunish($player, $this->request->get('message'));
                            }
                        } else {
                            throw new RconException(400, 'No player selected.');
                        }
                        break;

                    case 'forgive':
                        $this->hasPermission('admin.scoreboard.forgive');

                        if ($this->request->has('players')) {
                            foreach ($players as $player) {
                                $scoreboard->adminForgive($player, $this->request->get('message'));
                            }
                        } else {
                            throw new RconException(400, 'No player selected.');
                        }
                        break;

                    default:
                        throw new NotFoundHttpException();
                }

                if (! isset($data)) {
                    $data = [];
                }

                return MainHelper::response($data, null, null, null, false, true);
            }
        } catch (PlayerNotFoundException $e) {
            return MainHelper::response(null, $e->getMessage(), 'error', null, false, true);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('No server found with id %s', $id));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Server $server
     *
     * @return mixed
     */
    public function extras(Server $server)
    {
        $cacheKeyMapsPopular = sprintf('api.servers.%s.extra.maps.popular', $server->ServerID);
        $cacheKeyMaps = sprintf('api.servers.%s.extra.maps', $server->ServerID);
        $cacheKeyPop = sprintf('api.servers.%s.extra.pop', $server->ServerID);

        $mapsPopular = $this->cache->remember($cacheKeyMapsPopular, 60 * 24, function () use (&$server) {
            return $server->maps()->popular(Carbon::parse('-2 Week'))->get()->map(function ($map) use (&$server) {
                $mapname = Battlefield::mapName($map->MapName, $server->maps_file_path, $map->Gamemode);
                $gamemode = Battlefield::playmodeName($map->Gamemode, $server->modes_file_path);

                return [
                    'name' => sprintf('%s (%s)', $mapname, $gamemode),
                    'y'    => (int) $map->Total,
                ];
            });
        });

        $population = $this->cache->remember($cacheKeyPop, 60 * 24, function () use (&$server) {
            $sql = File::get(storage_path('sql/populationHistory.sql'));
            $results = collect($this->db->select($sql, [$server->ServerID]));

            return $results->map(function ($result) {
                $timestamp = Carbon::parse($result->SelectedDate)->getTimestamp();

                return [
                    $timestamp * 1000,
                    (int) $result->PlayerAvg,
                ];
            });
        });

        $rounds = $server->rounds()->since(Carbon::parse('-1 week'))->bare()->get();

        $maps = $this->cache->remember($cacheKeyMaps, 60, function () use (&$server) {
            return $server->maps()->since(Carbon::parse('-2 Weeks'))->get()->map(function ($map) use (&$server) {
                $mapname = Battlefield::mapName($map->MapName, $server->maps_file_path, $map->Gamemode);
                $gamemode = Battlefield::playmodeName($map->Gamemode, $server->modes_file_path);

                return [
                    'map_load'    => $map->TimeMapLoad->toIso8601String(),
                    'round_start' => $map->TimeRoundStarted->toIso8601String(),
                    'round_end'   => $map->TimeRoundEnd->toIso8601String(),
                    'map_name'    => $mapname,
                    'gamemode'    => $gamemode,
                    'rounds'      => $map->NumberofRounds,
                    'players'     => [
                        'min'  => $map->MinPlayers,
                        'max'  => $map->MaxPlayers,
                        'avg'  => $map->AvgPlayers,
                        'join' => $map->PlayersJoinedServer,
                        'left' => $map->PlayersLeftServer,
                    ],
                ];
            });
        });

        $data = [
            'maps_popular' => $mapsPopular,
            'population'   => $population,
            'rounds'       => $rounds,
            'maps'         => $maps,
        ];

        return MainHelper::response($data, null, null, null, false, true);
    }

    /**
     * Quick function for checking permissions for the scoreboard admin.
     *
     * @param string $permission Name of the permission
     * @param string $message    [description]
     *
     * @return bool [description]
     */
    private function hasPermission($permission, $message = 'You do have permission to issue this command')
    {
        if (! $this->user->ability(null, $permission)) {
            throw new AccessDeniedHttpException($message);
        }

        return true;
    }
}
