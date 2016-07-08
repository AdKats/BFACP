<?php

namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class HomeController.
 */
class HomeController extends Controller
{
    /**
     * Shows the dashboard.
     */
    public function index()
    {
        $playerRepository = app(PlayerRepository::class);

        // Cache results for 1 day
        $uniquePlayers = $this->cache->remember('players.unique.total', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayerCount();
        });

        // Cache results for 1 day
        $adkats_statistics = $this->cache->remember('adkats.statistics', 60 * 24, function () use (&$playerRepository) {
            $results = DB::select(File::get(storage_path().'/sql/adkats_statistics.sql'));

            return head($results);
        });

        // Cache results for 1 day
        $countryMap = $this->cache->remember('players.seen.country', 60 * 24, function () use (&$playerRepository) {
            return $playerRepository->getPlayersSeenByCountry();
        });

        $countryMapTable = $countryMap;

        $latestReported = DB::select(DB::raw(file_get_contents(storage_path('sql/latestReportedPlayers.sql'))));

        return view('dashboard', compact('uniquePlayers', 'adkats_statistics', 'countryMap', 'countryMapTable',
            'latestReported'))->with('page_title', trans('navigation.main.items.dashboard.title'));
    }
}
