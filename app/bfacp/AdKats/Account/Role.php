<?php namespace BFACP\AdKats\Account;

use BFACP\Elegant;

class Role extends Elegant
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_roles';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['role_id'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function permissions()
    {
        return $this->belongsToMany('BFACP\AdKats\Command', 'adkats_rolecommands', 'role_id', 'command_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function users()
    {
        return $this->hasMany('BFACP\AdKats\Account\User', 'user_role');
    }

    /**
     * Return the power level of the role
     * @param $count
     * @return int
     */
    public function getPowerLevel($count)
    {
        return ($count) + (2 * $this->permissions()->admin()->count());
    }
}
