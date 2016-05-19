<?php

namespace BFACP\Repositories;

use BFACP\Battlefield\Game;
use BFACP\Battlefield\Reputation;

/**
 * Class ReputationRepository.
 */
class ReputationRepository extends BaseRepository
{
    /**
     * @param int $limit
     *
     * @return mixed
     */
    public function getTopReputable($limit = 30)
    {
        $rep = Reputation::with('player')->orderBy('total_rep_co', 'desc')->take($limit)->get();

        return $rep;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getTopReputableByGame()
    {
        $rep = Game::with([
            'reputations' => function ($query) {
                $query->orderBy('total_rep_co', 'desc')->take(100);
            },
            'reputations.player',
        ])->get();

        return $rep;
    }
}
