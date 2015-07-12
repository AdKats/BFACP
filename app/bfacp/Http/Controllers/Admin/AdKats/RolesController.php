<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Account\Role;
use BFACP\AdKats\Command as Command;
use BFACP\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\View as View;

class RolesController extends BaseController
{
    public function index()
    {
        $guestCommandCount = Command::guest()->count();

        $roles = Role::with('users')->orderBy('role_name')->get();

        return View::make('admin.adkats.roles.index', compact('roles', 'guestCommandCount'));
    }

    public function edit($id)
    {
        try {
            return Role::with('permissions', 'users')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.roles.index')->withErrors([
                sprintf('No role found with ID #%s.', $id),
            ]);
        }
    }
}
