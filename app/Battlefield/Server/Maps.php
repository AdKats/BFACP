<?php

namespace BFACP\Battlefield\Server;

use BFACP\Elegant;
use Carbon\Carbon;

/**
 * Class Stats.
 */
class Maps extends Elegant
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
    protected $table = 'tbl_mapstats';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'ServerID';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['ServerID'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['TimeMapLoad', 'TimeRoundStarted', 'TimeRoundEnd'];

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
     * @param        $query
     * @param Carbon $timeframe
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopePopular($query, Carbon $timeframe)
    {
        return $query->where('TimeMapLoad', '>=', $timeframe)
            ->where(function($q) {
                $q->where('TimeRoundStarted', '!=', '0001-01-01 00:00:00');
                $q->where('TimeRoundEnd', '!=', '0001-01-01 00:00:00');
                $q->where('TimeMapLoad', '!=', '0001-01-01 00:00:00');
            })
            ->where('MapName', '!=', '')
            ->selectRaw("MapName, Gamemode, COUNT(ID) AS 'Total'")
            ->groupBy('MapName');
    }
}
