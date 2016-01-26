<?php

namespace BFACP\Battlefield\Scoreboard;

use BFACP\Elegant;

/**
 * Class Scores.
 */
class Scores extends Elegant
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
    protected $table = 'tbl_teamscores';

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
    public function server()
    {
        return $this->belongsTo(\BFACP\Battlefield\Server\Server::class, 'ServerID');
    }
}
