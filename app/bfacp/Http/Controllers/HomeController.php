<?php namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
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

        return View::make('dashboard', compact('uniquePlayers'))
            ->with('page_title', 'Dashboard');
    }
}
