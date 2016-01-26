<?php

namespace BFACP\Adkats\Account;

use BFACP\Elegant;

/**
 * Class User.
 */
class User extends Elegant
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
    protected $table = 'adkats_users';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['user_expiration'];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = ['stamp'];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function role()
    {
        return $this->belongsTo(\BFACP\Adkats\Account\Role::class, 'user_role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function soldiers()
    {
        return $this->hasMany(\BFACP\Adkats\Account\Soldier::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function getStampAttribute()
    {
        return $this->user_expiration->toIso8601String();
    }
}
