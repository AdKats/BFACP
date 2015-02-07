<?php namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
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
        return View::make('player.listing')
                ->with('page_title', 'Player Listing');
    }
}
