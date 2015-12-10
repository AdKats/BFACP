<?php namespace BFACP\Adkats\Infractions;

use BFACP\Elegant;

class Overall extends Elegant
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
    protected $table = 'adkats_infractions_global';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = ['history.type', 'history.action'];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function servers()
    {
        return $this->hasMany('BFACP\Adkats\Infractions\Server', 'player_id', 'player_id');
    }

    /**
     * Returns the infraction history
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function history()
    {
        return $this->hasMany('BFACP\Adkats\Record', 'target_id')->whereIn('command_type',
            [9, 10])->orderBy('record_time', 'desc');
    }
}
