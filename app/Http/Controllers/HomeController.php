<?php

namespace BFACP\Http\Controllers;

use BFACP\Adkats\Record;
use BFACP\Repositories\PlayerRepository;
use Carbon\Carbon;
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

        $countryMapTable = $countryMap->sortByDesc('total')->take(5);

        $timestamp = Carbon::now()->subWeeks(2);
        $latestReported = Record::whereIn('command_type', [18, 20])->where('record_time', '>=',
            $timestamp)->groupBy('target_id')->select(DB::raw('target_id, target_name, COUNT(record_id) AS `Total`, MAX(record_time) AS `Recent`'))->orderBy('Recent',
            'DESC')->having('Total', '>=', 5)->get();

        return view('dashboard', compact('uniquePlayers', 'adkats_statistics', 'countryMap', 'countryMapTable',
            'latestReported'))->with('page_title', trans('navigation.main.items.dashboard.title'));
    }
}
