<?php namespace BFACP\Account;

use BFACP\Elegant;

class Setting extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_settings_users';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = [];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

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
     * @var array
     */
    public static $rules = [
        'gravatar' => 'email',
        'timezone' => 'timezone',
        'lang'     => 'string'
    ];

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('BFACP\Account\User', 'user_id');
    }
}
