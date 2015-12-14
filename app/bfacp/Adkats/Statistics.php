<?php namespace BFACP\Adkats;

use BFACP\Elegant;

class Statistics extends Elegant
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
    protected $table = 'adkats_statistics';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'stat_id';

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
    protected $dates = ['stat_time'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'target_id');
    }

    /**
     * Only get certian types.
     *
     * @param        $query
     * @param  array $type
     *
     * @return object
     */
    public function scopeOfTypes($query, $type)
    {
        return $query->whereIn('stat_type', $type);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server\Server', 'server_id')->select([
            'ServerID',
            'ServerName',
            'GameID',
        ]);
    }
}
