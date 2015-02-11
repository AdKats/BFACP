<?php namespace BFACP\Battlefield;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;
use MainHelper;

class Server extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tbl_server';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'ServerID';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['*'];

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
    protected $appends = ['percentage', 'ip', 'port'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['game'];

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
    public function scoreboard()
    {
        return $this->hasMany('BFACP\Battlefield\Scoreboard\Scoreboard', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scores()
    {
        return $this->hasMany('BFACP\Battlefield\Scoreboard\Scores', 'ServerID');
    }

    public function scopeActive($query)
    {
        return $query->where('ConnectionState', 'on');
    }

    public function getPercentageAttribute()
    {
        return MainHelper::percent($this->usedSlots, $this->maxSlots);
    }

    public function getIPAttribute()
    {
        $host = explode(":", $this->IP_Address)[0];
        return gethostbyname($host);
    }

    public function getPortAttribute()
    {
        return (int) explode(":", $this->IP_Address)[1];
    }
}
