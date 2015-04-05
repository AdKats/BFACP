<?php namespace BFACP;

use BFACP\Elegant;
use Carbon\Carbon;

class Option extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_options';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['option_id'];

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
}
