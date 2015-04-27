<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Http\Controllers\BaseController;
use Carbon\Carbon;
use Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use MainHelper;
use BFACP\Account\User;

class UsersController extends BaseController
{
    public function index()
    {
        $users = User::with('roles', 'setting')->orderBy('username')->paginate(60);

        return View::make('admin.site.users.index', compact('users'))
            ->with('page_title', Lang::get('navigation.admin.site.items.users.title'));;
    }
}
