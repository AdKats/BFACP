<?php namespace BFACP\Repositories;

use BFACP\Battlefield\Player;
use BFACP\Exceptions\PlayerNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class PlayerRepository extends BaseRepository
{
    /**
     * Eager loading options
     * @var array
     */
    private $opts = [
        'ban.previous',
        'reputation',
        'infractionsGlobal',
        'infractionsServer.server',
        'dogtags.victim',
        'stats.weapons.weapon',
        'stats.server'
    ];

    /**
     * Returns a paginate result of all players
     * @return
     */
    public function getAllPlayers($limit = 100, $names = NULL)
    {
        if($limit === FALSE || $limit > 100) $limit = 100;

        $query = Player::with(
            'ban',
            'infractionsGlobal',
            'infractionsServer.server',
            'reputation'
        );

        if( ! empty($names) )
        {
            $query->where(function($q) use($names)
            {
                foreach(explode(',', $names) as $name)
                {
                    // Checks if string is an EAGUID
                    if(preg_match("/^EA_([0-9A-Z]{32}+)$/", $name, $matches))
                    {
                        $eaguid = sprintf("EA_%s", $matches[1]);
                        $q->orWhere('EAGUID', '=', $eaguid);
                    }

                    // Checks if string is a PBGUID
                    elseif(preg_match("/^([a-f0-9]+)$/", $name, $matches))
                    {
                        $pbguid = trim($matches[1]);
                        $q->orWhere('PBGUID', '=', $pbguid);
                    }

                    // Checks if string is an IPv4 Address
                    elseif(preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|[0-9])$/", $name, $matches))
                    {
                        $ip = trim($name);
                        $q->orWhere('IP_Address', '=', $ip);
                    }

                    // Checks if string is a player name
                    elseif(preg_match("/^([a-zA-Z0-9_\-]+)$/", $name, $matches))
                    {
                        $name = sprintf("%s%%", $matches[1]);
                        $q->orWhere('SoldierName', 'LIKE', $name);
                    }
                }
            });

            $query->orderBy('SoldierName', 'ASC');

            return $query->paginate($limit);
        }

        $query->orderBy('PlayerID', 'ASC');

        return $query->simplePaginate($limit);
    }

    /**
     * Returns a player by their ID
     * @param  integer $id Database Player ID
     * @return object
     */
    public function getPlayerById($id)
    {
        try {
            return Player::with($this->opts)->findOrFail($id);
        } catch(ModelNotFoundException $e) {
            throw new PlayerNotFoundException(404, "Player Not Found");
        }
    }

    /**
     * Returns the player with the givin guid
     * @param  string $guid EA GUID
     * @return object
     */
    public function getPlayerByGuid($guid)
    {
        $player = Player::with($this->opts)->where('EAGUID', $guid)->get();

        if($player->count() > 0)
            return $player;

        throw new PlayerNotFoundException(404, "Player Not Found");
    }

    /**
     * Gets the total number of players in the database
     * @return integer
     */
    public function getPlayerCount()
    {
        $count = Cache::remember('player.count', 60, function() {
            return Player::count();
        });

        return intval($count);
    }

    /**
     * Sets which relations should be returned
     * @param  array $opts
     * @return $this
     */
    public function setopts($opts = [])
    {
        if(empty($opts))
            return $this;

        if(is_string($opts))
            $opts = explode(',', $opts);

        if( ! in_array('bans', $opts))
            unset($this->opts[0]);

        if( ! in_array('reputation', $opts))
            unset($this->opts[1]);

        if( ! in_array('infractions', $opts))
            unset($this->opts[2], $this->opts[3]);

        if( ! in_array('stats', $opts))
            unset($this->opts[4], $this->opts[5], $this->opts[6]);

        if( in_array('none', $opts))
            $this->opts = [];

        return $this;
    }
}
