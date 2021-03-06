<?php

namespace BFACP\Player;

use BFACP\Elegant;

/**
 * Class Dogtag.
 */
class Dogtag extends Elegant
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
    protected $table = 'tbl_dogtags';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'KillerID';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function killer()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'KillerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function victim()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'VictimID');
    }
}
