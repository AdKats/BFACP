<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Cache;

class PlayersController extends BaseController
{
    private $repository;

    public function __construct(PlayerRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function index()
    {
        $limit = $this->request->get('limit', false);

        $name = $this->request->get('player', null);

        $players = $this->repository->getAllPlayers($limit, $name);

        return MainHelper::response($players, null, null, null, false, true);
    }

    /**
     * Get a player by their player database id
     *
     * @param  integer $id
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function show($id)
    {
        // Cache key
        $key = sprintf('api.player.%u', $id);

        // Is there already a cached version for the player
        $isCached = Cache::has($key);

        // Get or Set cache for player
        $player = Cache::remember($key, 5, function () use ($id) {
            return $this->repository->setopts([
                'ban.previous',
                'reputation',
                'infractionsGlobal',
                'infractionsServer.server',
                'stats.server',
                'sessions.server',
            ], true)->getPlayerById($id)->toArray();
        });

        return MainHelper::response($player, null, null, null, $isCached, true);
    }

    /**
     * Gets the players record history
     *
     * @param  integer $id
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function showRecords($id)
    {
        $records = $this->repository->getPlayerRecords($id);

        return MainHelper::response($records, null, null, null, false, true);
    }

    /**
     * Gets the players chatlogs
     *
     * @param  integer $id
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function showChatlogs($id)
    {
        $chatlogs = $this->repository->getPlayerChat($id);

        return MainHelper::response($chatlogs, null, null, null, false, true);
    }

    /**
     * Gets the players sessions
     *
     * @param  integer $id
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function showSessions($id)
    {
        $sessions = $this->repository->getPlayerSessions($id);

        return MainHelper::response($sessions, null, null, null, false, true);
    }
}
