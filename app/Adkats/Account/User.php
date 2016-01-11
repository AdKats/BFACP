<?php namespace BFACP\Adkats\Account;

use BFACP\Elegant;

class User extends Elegant
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
    protected $table = 'adkats_users';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['user_expiration'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['stamp'];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function role()
    {
        return $this->belongsTo('BFACP\Adkats\Account\Role', 'user_role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function soldiers()
    {
        return $this->hasMany('BFACP\Adkats\Account\Soldier', 'user_id');
    }

    public function getStampAttribute()
    {
        return $this->user_expiration->toIso8601String();
    }
}
