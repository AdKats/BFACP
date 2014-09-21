<?php namespace ADKGamers\Webadmin\Controllers\Admin;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use User, Role, Preference, Permission;
use Zizaco\Confide\Facade AS Confide;

class RolePermsController extends \BaseController
{
    protected $layout = 'layout.main';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $roles = Role::all();

        View::share('title', 'Role Listing');

        $this->layout->content = View::make('admin.role_perms.rolelist')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        View::share('title', 'Create New Role');

        $permissions = [];

        foreach(Permission::all() as $perm)
        {
            $permissions[$perm->id]['info'] = $perm;
            $permissions[$perm->id]['hasAccess'] = FALSE;
        }

        $this->layout->content = View::make('admin.role_perms.createrole')->with('permissions', $permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
            'role_name' => 'required|unique:bfadmincp_roles,name'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@create')->withErrors($validator)->withInput();
        }

        $input_role_display_name = trim(Input::get('role_name'));

        $input_perms = Input::get('perms', array());

        $role = new Role;
        $role->name = $input_role_display_name;
        $role->save();
        $role->perms()->sync($input_perms);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index')->with('message', $input_role_display_name . ' has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        View::share('title', 'Viewing Role: ' . $role->name);

        $this->layout->content = View::make('admin.role_perms.showrole')->with('role', $role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $role = Role::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $role_perms = $role->permissions();

        $permissions = [];

        foreach(Permission::all() as $perm)
        {
            $permissions[$perm->id]['info'] = $perm;
            $permissions[$perm->id]['hasAccess'] = FALSE;

            foreach($role_perms as $role_perm)
            {
                if($perm->id == $role_perm->id)
                {
                    $permissions[$perm->id]['hasAccess'] = TRUE;
                }
            }
        }

        View::share('title', 'Editing Role: ' . $role->name);

        $this->layout->content = View::make('admin.role_perms.edit')->with('permissions', $permissions)->with('role', $role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $role = Role::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $rules = array(
            'role_name' => 'required|unique:bfadmincp_roles,name,' . $role->id . ',id'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@edit', [$id])->withErrors($validator);
        }

        $input_role_display_name = trim(Input::get('role_name'));

        $input_perms = Input::get('perms', array());

        $role->name = $input_role_display_name;
        $role->save();

        $role->perms()->sync($input_perms);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@edit', [$id])
                    ->with('message', 'Role permissions has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        if($role->id == 9)
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index')->withErrors([
                'You cannot delete the default registered role.'
            ]);
        }

        $name = $role->name;

        DB::table('bfadmincp_assigned_roles')->where('role_id', $role->id)->update(['role_id' => 9]);
        DB::table('bfadmincp_permission_role')->where('role_id', $role->id)->delete();
        $role->delete();

        $msg = sprintf("%s has been deleted.", $name);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\RolePermsController@index')->with('message', $msg);
    }
}
