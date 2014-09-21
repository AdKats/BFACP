<?php namespace ADKGamers\Webadmin\Controllers\Admin\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommand;
use ADKGamers\Webadmin\Models\AdKats\Permission AS AdKatsPerm;
use ADKGamers\Webadmin\Models\AdKats\Role AS AdKatsRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
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
        $roles = AdKatsRole::all();

        View::share('title', 'Role Listing');

        $this->layout->content = View::make('admin.adkats.role_perms.rolelist')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        View::share('title', 'Create New Role');

        $commands = AdKatsCommand::where('command_active', 'Active')->get();

        $permissions = [];

        foreach($commands as $command)
        {
            $permissions[$command->command_key]['info'] = $command;
            $permissions[$command->command_key]['hasAccess'] = FALSE;
        }

        $this->layout->content = View::make('admin.adkats.role_perms.createrole')->with('permissions', $permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = [
            'role_name' => 'required|unique:adkats_roles,role_name'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@create')->withErrors($validator)->withInput();
        }

        $editRoleName = trim(Input::get('role_name'));

        $role = new AdKatsRole;
        $role->role_name = $editRoleName;
        $role->role_key = strtolower( snake_case( preg_replace('/\s+/', '', $editRoleName ) ) );
        $role->save();

        $temp = [];

        foreach(Input::get('perms') as $perm)
        {
            $temp[] = [
                'role_id' => $role->role_id,
                'command_id' => $perm
            ];
        }

        AdKatsPerm::insert($temp);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $role = AdKatsRole::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        View::share('title', 'Viewing Role: ' . $role->role_name);

        $this->layout->content = View::make('admin.adkats.role_perms.showrole')->with('role', $role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $role = AdKatsRole::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $commands = AdKatsCommand::where('command_active', 'Active')->get();

        $role_perms = $role->permissions;

        $permissions = [];

        foreach($commands as $command)
        {
            $permissions[$command->command_key]['info'] = $command;
            $permissions[$command->command_key]['hasAccess'] = FALSE;

            foreach($role_perms as $perm)
            {
                if($perm->command_id == $command->command_id)
                {
                    $permissions[$command->command_key]['hasAccess'] = TRUE;
                }
            }
        }

        View::share('title', 'Editing Role: ' . $role->role_name);

        $this->layout->content = View::make('admin.adkats.role_perms.editrole')->with('permissions', $permissions)->with('role', $role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $role = AdKatsRole::find($id);

        if(!$role)
        {
            return View::make('error.generror')->with('code', 404)->with('errmsg', 'PAGE NOT FOUND')
                            ->with('errdescription', 'No role exists with ID ' . $id)
                            ->with('title', 'Page Not Found');
        }

        $editRoleName = trim(Input::get('role_name'));

        $role->role_name = $editRoleName;

        if($role->role_key != "guest_default")
        {
            $role->role_key = strtolower( snake_case( preg_replace('/\s+/', '', $editRoleName ) ) );
        }

        $role->save();

        AdKatsPerm::destroy($id);

        $temp = [];

        foreach(Input::get('perms') as $perm)
        {
            $temp[] = [
                'role_id' => $id,
                'command_id' => $perm
            ];
        }

        AdKatsPerm::insert($temp);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@edit', [$id])
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
        $role = AdKatsRole::find($id);

        if($role->role_key == "guest_default")
        {
            return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index')->withErrors([
                'You cannot delete the default guest role.'
            ]);
        }

        $name = $role->role_name;

        DB::update("UPDATE `adkats_users` SET `user_role` = 1 WHERE `user_role` = :role_id", ['role_id' => $role->role_id]);

        $role->delete();

        $msg = sprintf("%s has been deleted.", $name);

        return Redirect::action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\RolePermsController@index')->with('message', $msg);
    }
}
