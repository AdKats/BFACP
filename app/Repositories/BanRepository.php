<?php

namespace BFACP\Repositories;

use BFACP\Adkats\Ban;
use Illuminate\Support\Facades\Cache;

/**
 * Class BanRepository.
 */
class BanRepository extends BaseRepository
{
    /**
     * Gets the latest bans.
     *
     * @param string $cacheKey Caching key to use
     * @param int    $ttl      Cache for X minutes
     *
     * @return array
     */
    public function getLatestBans($cacheKey = 'bans.latest', $ttl = 1)
    {
        $bans = Cache::remember($cacheKey, $ttl, function () {
            return Ban::with('player', 'record')->latest(30)->get()->toArray();
        });

        return $bans;
    }

    /**
     * Gets the bans of the users soldiers.
     *
     * @param array $ids Player IDs
     *
     * @return array
     */
    public function getPersonalBans($ids = [])
    {
        $bans = Ban::with('player', 'record')->personal($ids)->paginate(100);

        return $bans;
    }

    /**
     * Gets the banlist.
     *
     * @param int $limit Results to return
     *
     * @return object
     */
    public function getBanList($limit = 100)
    {
        $bans = Ban::with('player', 'record')->orderBy('ban_startTime', 'desc');

        if ($this->request->has('player')) {
            $bans = $bans->whereHas('player', function ($query) {
                $query->where('SoldierName', 'LIKE', sprintf('%s%%', $this->request->get('player')));
            });
        }

        return $bans->paginate($limit);
    }

    /**
     * Gets a ban by their ID.
     *
     * @param int $id Ban ID
     *
     * @return object
     */
    public function getBanById($id)
    {
        $ban = Ban::with('player', 'record')->findOrFail($id);

        return $ban;
    }
}
