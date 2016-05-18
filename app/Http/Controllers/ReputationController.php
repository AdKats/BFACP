<?php

namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Reputation;

/**
 * Class ReputationController.
 */
class ReputationController extends Controller
{
    /**
     * Shows the reputation listing.
     */
    public function index()
    {
        $rep = Reputation::paginate(30);

        return $rep;
    }
}
