<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\Adkats\Account\Role;
use BFACP\Adkats\Account\Soldier;
use BFACP\Adkats\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use Carbon\Carbon;
use Former\Facades\Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UsersController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the user listing
     */
    public function index()
    {
        $users = User::join('adkats_roles AS ar', 'ar.role_id', '=', 'adkats_users.user_role')->with('role',
            'soldiers.player')->orderBy('ar.role_name')->orderBy('user_name')->get();

        return View::make('admin.adkats.users.index', compact('users'))->with('page_title',
            Lang::get('navigation.admin.adkats.items.users.title'));
    }

    /**
     * Create a new user
     */
    public function store()
    {
        $v = Validator::make(Input::all(), [
            'username' => 'required|unique:adkats_users,user_name|alpha_dash',
        ]);

        if ($v->fails()) {
            return MainHelper::response(null, $v->messages()->first('username'), 'error', 400);
        }

        $user = new User();
        $user->user_name = Input::get('username');
        $user->user_role = 1;
        $user->user_expiration = Carbon::now()->addYears(20);
        $user->save();

        return MainHelper::response([
            'url' => route('admin.adkats.users.edit', $user->user_id),
        ]);
    }

    /**
     * Show the editing page
     *
     * @param integer $id User ID
     */
    public function edit($id)
    {
        try {

            $user = User::with('role', 'soldiers.player')->findOrFail($id);

            $roles = Role::lists('role_name', 'role_id');

            $page_title = Lang::get('navigation.admin.adkats.items.users.items.edit.title', ['id' => $id]);

            Former::populate($user);

            return View::make('admin.adkats.users.edit', compact('user', 'page_title', 'roles'));

        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.users.index')->withErrors([sprintf('User #%u doesn\'t exist.', $id)]);
        }
    }

    /**
     * Update user
     *
     * @param  integer $id User ID
     */
    public function update($id)
    {
        try {
            $user = User::findOrFail($id);

            $username = trim(Input::get('user_name', null));
            $email = trim(Input::get('user_email', null));
            $roleId = trim(Input::get('user_role', null));
            $expiration = trim(Input::get('user_expiration', null));
            $notes = trim(Input::get('user_notes', 'No Notes'));
            $soldiers = explode(',', Input::get('soldiers', ''));

            $v = Validator::make(Input::all(), [
                'user_name'  => 'required|alpha_dash',
                'user_email' => 'email',
                'user_role'  => 'required|exists:adkats_roles,role_id',
                'user_notes' => 'max:1000',
            ]);

            if ($v->fails()) {
                return Redirect::route('admin.adkats.users.edit', [$id])->withErrors($v)->withInput();
            }

            if (Input::has('user_name') && $user->user_name != $username) {
                $user->user_name = $username;
            }

            if (Input::has('user_email') && $user->user_email != $email) {
                $user->user_email = $email;
            }

            if ($user->user_role != $roleId) {
                $user->user_role = $roleId;
            }

            if (Input::has('user_expiration')) {
                $user->user_expiration = Carbon::parse($expiration)->toDateTimeString();
            } else {
                $user->user_expiration = Carbon::now()->addYears(20)->toDateTimeString();
            }

            // Always save the notes field
            $user->user_notes = $notes;

            $soldier_ids = [];

            $user->soldiers()->delete();

            if (Input::has('soldiers')) {
                foreach ($soldiers as $soldier) {
                    $soldier_ids[] = new Soldier(['player_id' => $soldier]);
                }
            }

            if (Input::has('soldier')) {
                $players = Player::where('SoldierName', Input::get('soldier'))->lists('PlayerID');

                foreach ($players as $player) {
                    if (!in_array($player, $soldiers)) {
                        $soldier_ids[] = new Soldier(['player_id' => $player]);
                    }
                }
            }

            if (!empty($soldier_ids)) {
                $user->soldiers()->saveMany($soldier_ids);
            }

            $user->save();

            $this->messages[] = sprintf('Changes Saved!');

            return Redirect::route('admin.adkats.users.edit', [$id])->with('messages', $this->messages);

        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.users.index')->withErrors([sprintf('User #%u doesn\'t exist.', $id)]);
        }
    }

    /**
     * Delete user
     *
     * @param  integer $id User ID
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $username = $user->user_name;
            $user->delete();

            return MainHelper::response([
                'url' => route('admin.adkats.users.index'),
            ], sprintf('%s was deleted', $username));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.users.index')->withErrors([sprintf('User #%u doesn\'t exist.', $id)]);
        }
    }
}
