<?php namespace ADKGamers\Webadmin\Models\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Eloquent;

class Role extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_roles';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

     /**
     * Returns a list of permissions the role has access to
     * @return object
     */
    public function permissions()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\AdKats\\Permission');
    }

    /**
     * Returns a list of users the role is assigned to
     * @return object
     */
    public function users()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\AdKats\\User', 'user_role', 'role_id')->orderBy('user_name');
    }
}
