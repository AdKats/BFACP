<?php namespace BFACP\Http\Controllers;

use BFACP\Repositories\PlayerRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
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
}
