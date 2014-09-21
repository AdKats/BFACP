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

class User extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_users';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Converts columns to carbon objects
     * @var array
     */
    protected $dates = array( 'user_expiration' );

    /**
     * Returns all the soldiers assocated with the user
     * @return object
     */
    public function soldiers()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\AdKats\\Soldier');
    }

    /**
     * Returns the user role
     * @return object
     */
    public function role()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\AdKats\\Role', 'role_id', 'user_role');
    }

    /**
     * Returns a list of permissions the users has access to
     * @return object
     */
    public function permissions()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\AdKats\\Permission', 'role_id', 'user_role');
    }
}
