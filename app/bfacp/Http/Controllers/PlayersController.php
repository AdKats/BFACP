<?php namespace BFACP\Http\Controllers;

use BFACP\AdKats\Record;
use BFACP\Repositories\PlayerRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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

        $charts             = [];
        $charts['overview'] = new Collection(DB::select(File::get(storage_path() . '/sql/playerCommandOverview.sql'), [$id]));
        $charts['spline']   = new Collection(DB::select(File::get(storage_path() . '/sql/playerCommandHistory.sql'), [$id]));
        $charts['aliases']  = Record::where('command_type', 48)
            ->where('target_id', $id)
            ->select(DB::raw('record_message AS `player_name`, COUNT(record_id) AS `seen`'))
            ->groupBy('player_name')->get();

        $charts['iphistory'] = Record::where('command_type', 49)
            ->where('target_id', $id)
            ->where('record_message', '!=', 'No previous IP on record')
            ->select(DB::raw('record_message AS `ip`, COUNT(record_id) AS `seen`'))
            ->groupBy('ip')->get();

        $charts['overview'] = $charts['overview']->filter(function ($command) {
            if (intval($command->value) > 0) {
                return true;
            }
        })->map(function ($command) {
            return [
                $command->label,
                intval($command->value)
            ];
        });

        $charts['aliases'] = $charts['aliases']->map(function ($a) {
            return [
                $a->player_name,
                intval($a->seen)
            ];
        });

        $charts['iphistory'] = $charts['iphistory']->map(function ($ip) {
            return [
                $ip->ip,
                intval($ip->seen)
            ];
        });

        $page_title = !empty($player->ClanTag) ? sprintf('[%s] %s', $player->ClanTag, $player->SoldierName) : $player->SoldierName;

        return View::make('player.profile', compact('player', 'page_title', 'charts'));
    }
}
