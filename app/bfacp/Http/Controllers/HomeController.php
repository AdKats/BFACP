<?php namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\View;
use Cache;
use DB;
use File;

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

        $adkats_statistics = Cache::remember('adkats.statistics', 10080, function() use($uniquePlayers) {
            $sql = File::get(storage_path() . '/sql/adkats_statistics.sql');

            $results = DB::select($sql, [
                $uniquePlayers,
                $uniquePlayers,
                $uniquePlayers,
                $uniquePlayers,
                $uniquePlayers,
                $uniquePlayers,
                $uniquePlayers
            ]);

            return head($results);
        });

        return View::make('dashboard', compact('uniquePlayers', 'adkats_statistics'))
            ->with('page_title', 'Dashboard');
    }
}
