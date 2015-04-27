<?php namespace BFACP\AdKats\Account;

use BFACP\Elegant;
use Carbon\Carbon;

class User extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_users';

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
    protected $dates = ['user_expiration'];

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
    protected $appends = ['stamp'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function role()
    {
        return $this->belongsTo('BFACP\AdKats\Account\Role', 'user_role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function soldiers()
    {
        return $this->hasMany('BFACP\AdKats\Account\Soldier', 'user_id');
    }

    public function getStampAttribute()
    {
        return $this->user_expiration->toIso8601String();
    }
}
