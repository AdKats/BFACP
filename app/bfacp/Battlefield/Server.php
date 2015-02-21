<?php namespace BFACP\Battlefield;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model AS Eloquent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
    protected $appends = ['percentage', 'ip', 'port', 'in_queue'];

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
    public function setting()
    {
        return $this->hasOne('BFACP\Battlefield\Setting', 'server_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scores()
    {
        return $this->hasMany('BFACP\Battlefield\Scoreboard\Scores', 'ServerID');
    }

    /**
     * Only return servers that should be active
     */
    public function scopeActive($query)
    {
        return $query->where('ConnectionState', 'on');
    }

    /**
     * Calculates how full the server is represented by a percentage
     * @return float
     */
    public function getPercentageAttribute()
    {
        return MainHelper::percent($this->usedSlots, $this->maxSlots);
    }

    /**
     * Gets the IP Address
     * @return string
     */
    public function getIPAttribute()
    {
        $host = explode(":", $this->IP_Address)[0];
        return gethostbyname($host);
    }

    /**
     * Gets the RCON port from the IP Address
     * @return integer
     */
    public function getPortAttribute()
    {
        $port = explode(":", $this->IP_Address)[1];
        return (int) $port;
    }

    /**
     * Gets the number of players currently in queue and caches the result for 1 minute
     * @return integer
     */
    public function getInQueueAttribute()
    {
        $result = Cache::remember('server.' . $this->ServerID . '.queue', 1, function()
        {
            $battlelog = App::make('BFACP\Libraries\Battlelog\BattlelogServer');

            return $battlelog->server($this->ServerID)->inQueue();
        });

        return $result;
    }
}
