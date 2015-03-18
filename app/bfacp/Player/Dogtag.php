<?php namespace BFACP\Player;

use BFACP\Elegant;
use Carbon\Carbon;

class Dogtag extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tbl_dogtags';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'KillerID';

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
    public $timestamps = false;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function killer()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'KillerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function victim()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'VictimID');
    }
}
