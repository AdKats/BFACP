<?php

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    /**
     * Validation rules
     * @var array
     */
    public static $rules = array(
        'name' => 'required|between:4,255'
    );

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bfadmincp_roles';

    /**
     * Returns a list of permissions the role has access to
     * @return object
     */
    public function permissions()
    {
        return DB::table('bfadmincp_permission_role')->where('role_id', $this->id)
                ->join('bfadmincp_permissions', 'bfadmincp_permission_role.permission_id', '=', 'bfadmincp_permissions.id')
                ->select('bfadmincp_permissions.*')->get();
    }

    /**
     * Returns a list of users the role is assigned to
     * @return object
     */
    public function users()
    {
        return DB::table('bfadmincp_assigned_roles')->where('role_id', $this->id)
                ->join('bfadmincp_users', 'bfadmincp_assigned_roles.user_id', '=', 'bfadmincp_users.id')
                ->select('bfadmincp_users.id', 'bfadmincp_users.username')->get();
    }
}
