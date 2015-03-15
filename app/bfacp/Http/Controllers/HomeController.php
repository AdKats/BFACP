<?php namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Game;
use BFACP\Battlefield\Server;
use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class HomeController extends BaseController
{
    private $playerRepo;

    public function __construct(PlayerRepository $playerRepo)
    {
        $this->playerRepo = $playerRepo;
        parent::__construct();
    }

    public function index()
    {
        $uniquePlayers = $this->playerRepo->getPlayerCount();

        $adkats_statistics = Cache::remember('adkats.statistics', 10080, function() {
            $results = DB::select( File::get(storage_path() . '/sql/adkats_statistics.sql') );
            return head($results);
        });

        $countryMap = $this->playerRepo->getPlayersSeenByCountry();

        return View::make('dashboard', compact('uniquePlayers', 'adkats_statistics', 'countryMap'))
            ->with('page_title', 'Dashboard');
    }

    public function scoreboard()
    {
        $games = Game::with(['servers' => function($query) {
            $query->active()->orderBy('ServerName');
        }])->get();

        return View::make('scoreboard', compact('games'))->with('page_title', 'Live Scoreboard');
    }

    public function chatlogs()
    {
        $games = Game::with(['servers' => function($query) {
            $query->active()->orderBy('ServerName');
        }])->get();

        return View::make('chatlogs', compact('games'))->with('page_title', 'Chatlogs');
    }
}
