<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Adkats\Account\Role;
use BFACP\Adkats\Command as Command;
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
    /**
     * @return $this
     */
    public function index()
    {
        $guestCommandCount = Command::guest()->count();
        $roles = Role::with('users')->orderBy('role_name')->get();

        return view('admin.adkats.roles.index', compact('roles', 'guestCommandCount'))->with('page_title',
            trans('navigation.admin.adkats.items.roles.title'));
    }

    /**
     * @return mixed
     */
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

            $permissions[$group][$command_id] = $command_name;
        });

        return view('admin.adkats.roles.create', compact('permissions'))->with('page_title',
            trans('navigation.admin.adkats.items.roles.items.create.title'));
    }

    /**
     * @param $id
     *
     * @return mixed
     */
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

                $permissions[$group][$command_id] = $command_name;
            });

            return view('admin.adkats.roles.edit', compact('permissions', 'role'))->with('page_title',
                trans('navigation.admin.adkats.items.roles.items.edit.title'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.adkats.roles.index')->withErrors([
                sprintf('No role found with ID #%s.', $id),
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function update($id)
    {
        try {
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

            $role->permissions()->sync($permissions->toArray());

            if ($this->request->get('display_name') != $role->role_name && $role->role_id != 1) {
                $role->role_name = $this->request->get('display_name');
                $role->save();
            }

            return redirect()->route('admin.adkats.roles.edit', $id)->withMessages([
                'Role Updated!',
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.adkats.roles.edit', $id)->withErrors([
                sprintf('No role found with ID #%s.', $id),
            ]);
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
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
            return redirect()->route('admin.adkats.roles.index')->withErrors([
                sprintf('Role #%u doesn\'t exist.', $id),
            ]);
        }
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
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
                'role_name' => 'required',
            ]);

            if ($v->fails()) {
                return redirect()->route('admin.adkats.roles.create')->withErrors($v)->withInput();
            }

            $role->role_name = $this->request->get('role_name');
            $role->save();

            // Update role permissions
            $role->permissions()->sync($permissions->toArray());

            return redirect()->route('admin.adkats.roles.edit', $role->role_id)->withMessages([
                'Role Created!',
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.adkats.roles.index');
        }
    }
}
