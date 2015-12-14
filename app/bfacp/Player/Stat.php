<?php namespace BFACP\Player;

use BFACP\Elegant;

class Stat extends Elegant
{
    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'tbl_playerstats';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'StatsID';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['FirstSeenOnServer', 'LastSeenOnServer'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['first_seen', 'last_seen'];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsToMany('BFACP\Battlefield\Server\Server', 'tbl_server_player', 'StatsID', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function weapons()
    {
        return $this->belongsToMany('BFACP\Player\Weapon', 'tbl_server_player', 'StatsID', 'StatsID');
    }

    public function getFirstSeenAttribute()
    {
        return $this->FirstSeenOnServer->toIso8601String();
    }

    public function getLastSeenAttribute()
    {
        return $this->LastSeenOnServer->toIso8601String();
    }
}
