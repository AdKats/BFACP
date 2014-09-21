<?php

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface
{
    use ConfideUser;
    use HasRole;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bfadmincp_users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = array('username', 'email', 'password', 'confirmed');

    /**
     * Validation rules
     * @var array
     */
    public static $rules = array(
        'password' => 'between:6,32|confirmed',
        'password_confirmation' => 'between:6,32',
        'username' => 'unique:bfadmincp_users,username',
        'email'    => 'email'
    );

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Returns the users account preferences
     * @return object
     */
    public function preferences()
    {
        return $this->belongsTo('Preference', 'id', 'user_id');
    }

    public function group()
    {
        return DB::table('bfadmincp_assigned_roles')
                ->join('bfadmincp_roles', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_roles.id')
                ->where('user_id', $this->id)
                ->pluck('bfadmincp_roles.name');
    }

    public function roleId()
    {
        return DB::table('bfadmincp_assigned_roles')
                ->join('bfadmincp_roles', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_roles.id')
                ->where('user_id', $this->id)
                ->pluck('bfadmincp_roles.id');
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function permissions()
    {
        return DB::table('bfadmincp_assigned_roles')
                ->join('bfadmincp_permission_role', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_permission_role.role_id')
                ->join('bfadmincp_permissions', 'bfadmincp_permission_role.permission_id', '=', 'bfadmincp_permissions.id')
                ->where('user_id', $this->id)->get();
    }
}
