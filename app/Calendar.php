<?php

namespace BFACP;

/**
 * Class Option.
 */
class Calendar extends Elegant
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
    protected $table = 'bfacp_calendar';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'calendar_datetime';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['calendar_datetime'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['calendar_datetime'];

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
}
