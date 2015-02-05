<?php namespace BFACP\Battlefield;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Player extends Eloquent
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
    public $timestamps = FALSE;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = ['profile_url'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['game'];

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
     * Gets the URL to the players profile
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return route('player.show', [
            'id' => $this->PlayerID,
            'name' => $this->SoldierName
        ]);
    }
}
