<?php

namespace BFACP\Http\Controllers;

use BFACP\Repositories\ReputationRepository;

/**
 * Class ReputationController.
 */
class ReputationController extends Controller
{
    /**
     * @var ReputationRepository
     */
    protected $rep;

    /**
     * @param ReputationRepository $rep
     */
    public function __construct(ReputationRepository $rep)
    {
        parent::__construct();

        $this->rep = $rep;
    }

    /**
     * Shows the reputation listing.
     */
    public function index()
    {
        $games = $this->rep->getTopReputableByGame();

        return view('player.reputation', compact('games'));
    }
}
