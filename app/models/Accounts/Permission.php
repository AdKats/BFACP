<?php

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    /**
     * Validation rules
     * @var array
     */
    public static $rules = array(
        'name' => 'required|between:3,32'
    );

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = array('name', 'display_name');

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bfadmincp_permissions';
}
