<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Entrust Role Model
	|--------------------------------------------------------------------------
	|
	| This is the Role model used by Entrust to create correct relations.  Update
	| the role if it is in a different namespace.
	|
	*/
	'role'                  => 'BFACP\Account\Role',
	/*
	|--------------------------------------------------------------------------
	| Entrust Roles Table
	|--------------------------------------------------------------------------
	|
	| This is the Roles table used by Entrust to save roles to the database.
	|
	*/
	'roles_table'           => 'bfacp_roles',
	/*
	|--------------------------------------------------------------------------
	| Entrust Permission Model
	|--------------------------------------------------------------------------
	|
	| This is the Permission model used by Entrust to create correct relations.  Update
	| the permission if it is in a different namespace.
	|
	*/
	'permission'            => 'BFACP\Account\Permission',
	/*
	|--------------------------------------------------------------------------
	| Entrust Permissions Table
	|--------------------------------------------------------------------------
	|
	| This is the Permissions table used by Entrust to save permissions to the database.
	|
	*/
	'permissions_table'     => 'bfacp_permissions',
	/*
	|--------------------------------------------------------------------------
	| Entrust permission_role Table
	|--------------------------------------------------------------------------
	|
	| This is the permission_role table used by Entrust to save relationship between permissions and roles to the database.
	|
	*/
	'permission_role_table' => 'bfacp_permission_role',
	/*
	|--------------------------------------------------------------------------
	| Entrust assigned_roles Table
	|--------------------------------------------------------------------------
	|
	| This is the assigned_roles table used by Entrust to save assigned roles to the database.
	|
	*/
	'assigned_roles_table'  => 'bfacp_assigned_roles',

];
