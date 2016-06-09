<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Account\Permission;
use BFACP\Account\Role;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

/**
 * Class RolesController.
 */
class RolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('permission:admin.site.roles');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $roles = Role::with('users')->get();

        $page_title = trans('navigation.admin.site.items.roles.title');

        return view('admin.site.roles.index', compact('roles', 'page_title'));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $permissions = [];

        foreach (Permission::all() as $permission) {
            if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {

                // Uppercase the first letter
                $key = ucfirst($matches[1]);

                // Push to array
                $permissions[$key][$permission->id] = $permission->display_name;
            } else {

                // Push to array
                $permissions['General'][$permission->id] = $permission->display_name;
            }
        }

        $page_title = trans('navigation.admin.site.items.roles.items.create.title');

        return view('admin.site.roles.create', compact('permissions', 'page_title'));
    }

    /**
     * @return mixed
     */
    public function store()
    {
        try {
            $role = new Role();

            $permissions = new Collection($this->request->get('permissions', []));

            if ($this->request->has('permissions')) {
                $permissions = $permissions->filter(function ($id) {
                    if (is_numeric($id)) {
                        return true;
                    }
                })->map(function ($id) {
                    return (int) $id;
                });
            }

            $v = Validator::make($this->request->all(), [
                'role_name' => Role::$rules['name'],
            ]);

            if ($v->fails()) {
                return redirect()->route('admin.site.roles.create')->withErrors($v)->withInput();
            }

            $role->name = trim($this->request->get('role_name'));
            $role->save();

            $this->log->info(sprintf('%s created role %s.', $this->user->username, $role->name));

            // Update role permissions
            $role->permissions()->sync($permissions->toArray());

            return redirect()->route('admin.site.roles.edit', [$role->id])->with('messages', [
                'Role Created!',
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.site.roles.index');
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $role = Role::with('users', 'permissions')->findOrFail($id);

        $permissions = [];

        foreach (Permission::all() as $permission) {
            if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {

                // Uppercase the first letter
                $key = ucfirst($matches[1]);

                // Push to array
                $permissions[$key][$permission->id] = $permission->display_name;
            } else {

                // Push to array
                $permissions['General'][$permission->id] = $permission->display_name;
            }
        }

        $page_title = trans('navigation.admin.site.items.roles.items.edit.title', ['name' => $role->name]);

        return view('admin.site.roles.edit', compact('role', 'permissions', 'page_title'));
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function update($id)
    {
        // Disable rules on model
        Role::$rules = [];

        $role = Role::findOrFail($id);

        $permissions = new Collection($this->request->get('permissions', []));

        if ($this->request->has('permissions')) {
            $permissions = $permissions->filter(function ($id) {
                if (is_numeric($id)) {
                    return true;
                }
            })->map(function ($id) {
                return (int) $id;
            });
        }

        // Update role permissions
        $role->permissions()->sync($permissions->toArray());

        if ($this->request->get('display_name') != $role->name && ! in_array($role->id, [1, 2])) {
            $this->log->info(sprintf('%s changed role name from %s to %s.', $this->user->username, $role->name,
                $this->request->get('display_name')));
            $role->name = trim($this->request->get('display_name'));
            $role->save();
        } else {
            // Update timestamp
            $role->touch();
            $this->log->info(sprintf('%s updated role %s.', $this->user->username, $role->name));
        }

        return redirect()->route('admin.site.roles.edit', [$id])->with('messages', [
            'Role Updated!',
        ]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        // Disable rules on model
        Role::$rules = [];

        // Get role
        $role = Role::findOrFail($id);

        if (in_array($role->id, [1, 2])) {
            return MainHelper::response(null, sprintf('You can\'t delete the %s role.', $role->name), 'error');
        }

        // Save role name
        $roleName = $role->name;

        foreach ($role->users as $user) {
            $user->roles()->detach($id);
            $user->roles()->attach(2);
        }

        $role->delete();

        $this->log->info(sprintf('%s deleted role %s.', $this->user->username, $roleName));

        return MainHelper::response([
            'url' => route('admin.site.roles.index'),
        ], sprintf('%s was deleted', $roleName));
    }
}
