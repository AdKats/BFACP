<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\Adkats\Account\Role;
use BFACP\Adkats\Command as Command;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View as View;

class RolesController extends BaseController
{
    public function index()
    {
        $guestCommandCount = Command::guest()->count();
        $roles = Role::with('users')->orderBy('role_name')->get();

        return View::make('admin.adkats.roles.index', compact('roles', 'guestCommandCount'))->with('page_title',
            Lang::get('navigation.admin.adkats.items.roles.title'));
    }

    public function create()
    {
        $permissions = [];

        Command::type('Active')->get()->each(function ($permission) use (&$permissions) {
            $command_id = $permission->command_id;
            $command_name = $permission->command_name;

            if ($permission->is_interactive) {
                $group = 'Admin';
            } else {
                $group = 'Public';
            }

            $permissions[ $group ][ $command_id ] = $command_name;
        });

        return View::make('admin.adkats.roles.create', compact('permissions'))->with('page_title',
            Lang::get('navigation.admin.adkats.items.roles.items.create.title'));
    }

    public function edit($id)
    {
        try {
            $permissions = [];

            $role = Role::with('permissions', 'users')->findOrFail($id);

            Command::type('Active')->get()->each(function ($permission) use (&$permissions) {
                $command_id = $permission->command_id;
                $command_name = $permission->command_name;

                if ($permission->is_interactive) {
                    $group = 'Admin';
                } else {
                    $group = 'Public';
                }

                $permissions[ $group ][ $command_id ] = $command_name;
            });

            return View::make('admin.adkats.roles.edit', compact('permissions', 'role'))->with('page_title',
                Lang::get('navigation.admin.adkats.items.roles.items.edit.title'));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.roles.index')->withErrors([
                sprintf('No role found with ID #%s.', $id),
            ]);
        }
    }

    public function update($id)
    {
        try {
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

            $role->permissions()->sync($permissions->toArray());

            if (Input::get('display_name') != $role->role_name && $role->role_id != 1) {
                $role->role_name = Input::get('display_name');
                $role->save();
            }

            return Redirect::route('admin.adkats.roles.edit', $id)->withMessages([
                'Role Updated!',
            ]);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.roles.edit', $id)->withErrors([
                sprintf('No role found with ID #%s.', $id),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            // Get role
            $role = Role::findOrFail($id);

            if ($role->role_id == 1) {
                return MainHelper::response(null, sprintf('You can\'t delete the %s role.', $role->role_name), 'error');
            }

            // Save role name
            $roleName = $role->role_name;

            $guestRole = Role::findOrFail(1);

            foreach ($role->users as $user) {
                $user->role()->associate($guestRole)->save();
            }

            $role->delete();

            return MainHelper::response([
                'url' => route('admin.adkats.roles.index'),
            ], sprintf('%s was deleted', $roleName));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.roles.index')->withErrors([sprintf('Role #%u doesn\'t exist.', $id)]);
        }
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
                'role_name' => 'required',
            ]);

            if ($v->fails()) {
                return Redirect::route('admin.adkats.roles.create')->withErrors($v)->withInput();
            }

            $role->role_name = Input::get('role_name');
            $role->save();

            // Update role permissions
            $role->permissions()->sync($permissions->toArray());

            return Redirect::route('admin.adkats.roles.edit', $role->role_id)->withMessages([
                'Role Created!',
            ]);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.roles.index');
        }
    }
}
