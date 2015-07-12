<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Account\Permission;
use BFACP\Account\Role;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class RolesController extends BaseController
{
    public function index()
    {
        $roles = Role::with('users')->get();

        $page_title = Lang::get('navigation.admin.site.items.roles.title');

        return View::make('admin.site.roles.index', compact('roles', 'page_title'));
    }

    public function create()
    {
        $permissions = [];

        foreach (Permission::all() as $permission) {
            if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {

                // Uppercase the first letter
                $key = ucfirst($matches[1]);

                // Push to array
                $permissions[ $key ][ $permission->id ] = $permission->display_name;
            } else {

                // Push to array
                $permissions['General'][ $permission->id ] = $permission->display_name;
            }
        }

        $page_title = Lang::get('navigation.admin.site.items.roles.items.create.title');

        return View::make('admin.site.roles.create', compact('permissions', 'page_title'));
    }

    public function store()
    {
        try {
            $role = new Role();

            $permissions = new Collection(Input::get('permissions', []));

            if (Input::has('permissions')) {
                $permissions = $permissions->filter(function ($id) {
                    if (is_numeric($id)) {
                        return true;
                    }
                })->map(function ($id) {
                    return (int)$id;
                });
            }

            $v = Validator::make(Input::all(), [
                'role_name' => Role::$rules['name'],
            ]);

            if ($v->fails()) {
                return Redirect::route('admin.site.roles.create')->withErrors($v)->withInput();
            }

            $role->name = trim(Input::get('role_name'));
            $role->save();

            // Update role permissions
            $role->permissions()->sync($permissions->toArray());

            return Redirect::route('admin.site.roles.edit', [$role->id])->with('messages', [
                'Role Created!',
            ]);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.roles.index');
        }
    }

    public function edit($id)
    {
        try {
            $role = Role::with('users', 'permissions')->findOrFail($id);

            $permissions = [];

            foreach (Permission::all() as $permission) {
                if (preg_match('/^admin\\.([a-z]+)/A', $permission->name, $matches)) {

                    // Uppercase the first letter
                    $key = ucfirst($matches[1]);

                    // Push to array
                    $permissions[ $key ][ $permission->id ] = $permission->display_name;
                } else {

                    // Push to array
                    $permissions['General'][ $permission->id ] = $permission->display_name;
                }
            }

            $page_title = Lang::get('navigation.admin.site.items.roles.items.edit.title', ['name' => $role->name]);

            return View::make('admin.site.roles.edit', compact('role', 'permissions', 'page_title'));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.roles.index')->withErrors([sprintf('Role #%u doesn\'t exist.', $id)]);
        }
    }

    public function update($id)
    {
        try {
            // Disable rules on model
            Role::$rules = [];

            $role = Role::findOrFail($id);

            $permissions = new Collection(Input::get('permissions', []));

            if (Input::has('permissions')) {
                $permissions = $permissions->filter(function ($id) {
                    if (is_numeric($id)) {
                        return true;
                    }
                })->map(function ($id) {
                    return (int)$id;
                });
            }

            // Update role permissions
            $role->permissions()->sync($permissions->toArray());

            if (Input::get('display_name') != $role->name && !in_array($role->id, [1, 2])) {
                $role->name = trim(Input::get('display_name'));
                $role->save();
            } else {
                // Update timestamp
                $role->touch();
            }

            return Redirect::route('admin.site.roles.edit', [$id])->with('messages', [
                'Role Updated!',
            ]);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.roles.index')->withErrors([sprintf('Role #%u doesn\'t exist.', $id)]);
        }
    }

    public function destroy($id)
    {
        try {
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

            return MainHelper::response([
                'url' => route('admin.site.roles.index'),
            ], sprintf('%s was deleted', $roleName));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.roles.index')->withErrors([sprintf('Role #%u doesn\'t exist.', $id)]);
        }
    }
}
