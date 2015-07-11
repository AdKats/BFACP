<?php namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Game;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class HomeController extends BaseController
{
    /**
     * Shows the dashboard
     */
    public function index()
    {
        $playerRepository = App::make('BFACP\Repositories\PlayerRepository');

        // Cache results for 1 day
        $uniquePlayers = Cache::remember('players.unique.total', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayerCount();
        });

        // Cache results for 1 day
        $adkats_statistics = Cache::remember('adkats.statistics', 60 * 24, function () use (&$playerRepository) {
            $results = DB::select(File::get(storage_path() . '/sql/adkats_statistics.sql'));
            return head($results);
        });

        // Cache results for 1 day
        $countryMap = Cache::remember('players.seen.country', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayersSeenByCountry();
        });

        $countryMapTable = $countryMap->sortByDesc('total')->take(5);

        return View::make('dashboard', compact('uniquePlayers', 'adkats_statistics', 'countryMap', 'countryMapTable'))
            ->with('page_title', Lang::get('navigation.main.items.dashboard.title'));
    }

    /**
     * Shows the live scoreboard
     */
    public function scoreboard()
    {
        $games = Game::with(['servers' => function ($query) {
            $query->active()->orderBy('ServerName');
        }])->get();

        return View::make('scoreboard', compact('games'))->with('page_title', Lang::get('navigation.main.items.scoreboard.title'));
    }
}
