<?php namespace BFACP\Battlefield;

use BFACP\Elegant;
use Carbon\Carbon;
use MainHelper;

class Player extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tbl_playerdata';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'PlayerID';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['PlayerID'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = [];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = ['profile_url', 'country_flag', 'country_name', 'rank_image'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['game', 'battlelog'];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dogtags()
    {
        return $this->hasMany('BFACP\Player\Dogtag', 'KillerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function ban()
    {
        return $this->hasOne('BFACP\AdKats\Ban', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function stats()
    {
        return $this->hasManyThrough('BFACP\Player\Stat', 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function sessions()
    {
        return $this->hasManyThrough('BFACP\Player\Session', 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsGlobal()
    {
        return $this->hasOne('BFACP\AdKats\Infractions\Overall', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsServer()
    {
        return $this->hasMany('BFACP\AdKats\Infractions\Server', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function game()
    {
        return $this->belongsTo('BFACP\Battlefield\Game', 'GameID')->remember(10);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reputation()
    {
        return $this->hasOne('BFACP\Battlefield\Reputation', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function battlelog()
    {
        return $this->hasOne('BFACP\AdKats\Battlelog', 'player_id');
    }

    /**
     * Does the player have a battlelog persona id linked
     * @return boolean
     */
    public function hasPersona()
    {
        return !empty($this->battlelog->persona_id);
    }

    /**
     * Gets the URL to the players profile
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return route('player.show', [
            'id'   => $this->PlayerID,
            'name' => $this->SoldierName
        ]);
    }

    /**
     * Get the country name
     * @return string
     */
    public function getCountryNameAttribute()
    {
        if ($this->CountryCode == '--' || empty($this->CountryCode)) {
            return 'Unknown';
        }

        return MainHelper::countries($this->CountryCode);
    }

    /**
     * Get the country image flag
     * @return string
     */
    public function getCountryFlagAttribute()
    {
        if ($this->CountryCode == '--' || empty($this->CountryCode)) {
            return 'images/flags/24/_unknown.png';
        }

        return sprintf('images/flags/24/%s.png', strtoupper($this->CountryCode));
    }

    /**
     * Get the rank image
     * @return string
     */
    public function getRankImageAttribute()
    {
        switch ($this->game->Name) {
            case 'BF3':
                $rank = $this->GlobalRank;

                if ($rank > 45) {
                    if ($rank > 100) {
                        $rank = 100;
                    }

                    $path = sprintf('images/games/bf3/ranks/large/ss%u.png', $rank);
                } else {
                    $path = sprintf('images/games/bf3/ranks/large/r%u.png', $this->GlobalRank);
                }
                break;

            case 'BF4':
                $path = sprintf('images/games/bf4/ranks/r%u.png', $this->GlobalRank);
                break;

            case 'BFHL':
                $path = sprintf('images/games/bfhl/ranks/r%u.png', $this->GlobalRank);
                break;

            default:
                $path = null;
        }

        return $path;
    }
}
