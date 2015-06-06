<?php namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class PlayersController extends BaseController
{
    private $repository;

    public function __construct(PlayerRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function listing()
    {
        $page_title = Input::has('player') ? 'Player Search' : 'Player Listing';

        return View::make('player.listing', compact('page_title'));
    }

    public function profile($id, $name = '')
    {
        // Cache key
        $key = sprintf('player.%u', $id);

        // Is there already a cached version for the player
        $isCached = Cache::has($key);

        // Get or Set cache for player
        $player = Cache::remember($key, 5, function () use ($id) {
            $json = $this->repository->setopts([
                'ban.previous',
                'reputation',
                'infractionsGlobal',
                'infractionsServer.server',
                'stats.server'
            ], true)->getPlayerById($id)->toJson();

            return json_decode($json);
        });

        $page_title = !empty($player->ClanTag) ?
        sprintf('[%s] %s', $player->ClanTag, $player->SoldierName) : $player->SoldierName;

        return View::make('player.profile', compact('player', 'page_title'));
    }
}
