<?php namespace BFACP;


class Option extends Elegant
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
    protected $table = 'bfacp_options';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['option_id'];

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
    protected $with = [];

    /**
     * Quick method for getting an option setting
     *
     * @param $query
     * @param $optionKey
     *
     * @return mixed
     */
    public function scopeSetting($query, $optionKey)
    {
        return $query->where('option_key', $optionKey);
    }
}
