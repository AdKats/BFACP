<?php namespace BFACP\Account;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_permissions';

    /**
     * Table primary key
     * @var string
     */
    //protected $primaryKey = '';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['name', 'display_name'];

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
        'name' => 'required|unique:bfadmincp_permissions,name|between:3,32'
    ];
}
