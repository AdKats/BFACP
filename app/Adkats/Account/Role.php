<?php

namespace BFACP\Adkats\Account;

use BFACP\Elegant;

/**
 * Class Role.
 */
class Role extends Elegant
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
    protected $table = 'adkats_roles';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['role_id'];

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
    public function users()
    {
        return $this->hasMany(\BFACP\Adkats\Account\User::class, 'user_role');
    }

    /**
     * Return the power level of the role.
     *
     * @param $count
     *
     * @return int
     */
    public function getPowerLevel($count)
    {
        return ($count) + (2 * $this->permissions()->admin()->count());
    }

    /**
     * Trims $value and sets the role key.
     *
     * @param $value
     */
    public function setRoleNameAttribute($value)
    {
        $name = trim($value);
        $this->attributes['role_name'] = $name;
        $this->attributes['role_key'] = strtolower(str_replace(' ', '_', $name));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function permissions()
    {
        return $this->belongsToMany(\BFACP\Adkats\Command::class, 'adkats_rolecommands', 'role_id', 'command_id');
    }
}
