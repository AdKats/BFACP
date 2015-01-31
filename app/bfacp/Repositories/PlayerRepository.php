<?php namespace BFACP\Repositories;

use BFACP\Battlefield\Player;
use BFACP\Exceptions\PlayerNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    public function getAllPlayers($limit = 100)
    {
        if($limit === FALSE)
            $limit = 100;

        return Player::with($this->opts)->simplePaginate($limit);
    }

    /**
     * Returns a player by their ID
     * @param  integer $id Database Player ID
     * @return object
     */
    public function getPlayerById($id)
    {
        try
        {
            return Player::with($this->opts)->findOrFail($id);
        }
        catch(ModelNotFoundException $e)
        {
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
        {
            return $player;
        }

        throw new PlayerNotFoundException(404, "Player Not Found");
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
