<?php

namespace BFACP\Battlefield;

use BFACP\Elegant;
use Carbon\Carbon;

/**
 * Class Stats.
 */
class Round extends Elegant
{
    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'tbl_extendedroundstats';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'server_id';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['roundstat_time'];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server\Server', 'ServerID');
    }

    /**
     * @param $query
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeCurrent($query)
    {
        return $query->max('round_id');
    }

    /**
     * @param $query
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeRound($query, $id)
    {
        return $query->where('round_id', $id);
    }

    /**
     * @param        $query
     * @param Carbon $timeframe
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeSince($query, Carbon $timeframe)
    {
        return $query->where('roundstat_time', '>=', $timeframe);
    }

    /**
     * @param $query
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeBare($query)
    {
        return $query->groupBy('round_id')->selectRaw('round_id, MIN(roundstat_time) AS \'RoundStart\', MAX(roundstat_time) AS \'RoundEnd\'');
    }
}
