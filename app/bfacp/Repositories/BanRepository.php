<?php namespace BFACP\Repositories;

use BFACP\Battlefield\Player;
use BFACP\AdKats\Ban;
use BFACP\Exceptions\PlayerNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class BanRepository extends BaseRepository
{
	/**
	 * Gets the latest bans
	 * @param  string  $cacheKey Caching key to use
	 * @param  integer $ttl      Cache for X minutes
	 * @return array
	 */
	public function getLatestBans($cacheKey = 'bans.latest', $ttl = 1)
	{
		$bans = Cache::remember($cacheKey, $ttl, function() {
			return Ban::with('player', 'record')->latest(30)->get()->toArray();
		});

		return $bans;
	}

	/**
	 * Gets the bans of the users soldiers
	 * @param  array $ids Player IDs
	 * @return array
	 */
	public function getPersonalBans($ids = [])
	{
		$bans = Ban::with('player', 'record')->personal($ids)->get()->toArray();

		return $bans;
	}
}
