<?php namespace BFACP\AdKats;

use BFACP\Elegant;
use Carbon\Carbon;

class Locale extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkatmyisam_locales';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'locale_index';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['locale_index'];

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
    public $timestamps = TRUE;

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
     * Validation rules
     *
     * @var array
     */
    protected static $rules = [];

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        // Create the validation query to prevent duplicate entries.
        $query = sprintf('%s,locale_message,NULL,locale_id,locale_subset,%s,locale_lang,%s', $this->table, $this->locale_subset, $this->locale_lang);

        static::$rules = [
            'locale_subset' => 'required|alpha|min:1',
            'locale_message' => 'required|min:4|unique:' . $query,
            'locale_lang' => 'required|max:2|in:' . \MainHelper::languages(NULL, TRUE)
        ];
    }
}
