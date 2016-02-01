<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Adkats\Account\Role;
use BFACP\Adkats\Account\Soldier;
use BFACP\Adkats\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use Carbon\Carbon;
use Former\Facades\Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersController.
 */
class UsersController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');

        $this->middleware('permission:admin.adkats.user.view', [
            'only' => [
                'index',
            ],
        ]);

        $this->middleware('permission:admin.adkats.user.edit', [
            'except' => [
                'index',
            ],
        ]);
    }

    /**
     * Show the user listing.
     */
    public function index()
    {
        $users = User::join('adkats_roles AS ar', 'ar.role_id', '=', 'adkats_users.user_role')->with('role',
            'soldiers.player')->orderBy('ar.role_name')->orderBy('user_name')->get();

        return view('admin.adkats.users.index', compact('users'))->with('page_title',
            trans('navigation.admin.adkats.items.users.title'));
    }

    /**
     * Create a new user.
     */
    public function store()
    {
        $v = Validator::make($this->request->all(), [
            'username' => 'required|unique:adkats_users,user_name|alpha_dash',
        ]);

        if ($v->fails()) {
            return MainHelper::response(null, $v->messages()->first('username'), 'error', 400);
        }

        $user = new User();
        $user->user_name = $this->request->get('username');
        $user->user_role = 1;
        $user->user_expiration = Carbon::now()->addYears(20);
        $user->save();

        return MainHelper::response([
            'url' => route('admin.adkats.users.edit', $user->user_id),
        ]);
    }

    /**
     * Show the editing page.
     *
     * @param int $id User ID
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $user = User::with('role', 'soldiers.player')->findOrFail($id);

            $roles = Role::lists('role_name', 'role_id');

            $page_title = trans('navigation.admin.adkats.items.users.items.edit.title', ['id' => $id]);

            Former::populate($user);

            return view('admin.adkats.users.edit', compact('user', 'page_title', 'roles'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.adkats.users.index')->withErrors([
                sprintf('User #%u doesn\'t exist.', $id),
            ]);
        }
    }

    /**
     * Update user.
     *
     * @param int $id User ID
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        try {
            $user = User::findOrFail($id);

            $username = trim($this->request->get('user_name', null));
            $email = trim($this->request->get('user_email', null));
            $roleId = trim($this->request->get('user_role', null));
            $expiration = trim($this->request->get('user_expiration', null));
            $notes = trim($this->request->get('user_notes', 'No Notes'));
            $soldiers = explode(',', $this->request->get('soldiers', ''));

            $v = Validator::make($this->request->all(), [
                'user_name' => 'required|alpha_dash',
                'user_email' => 'email',
                'user_role' => 'required|exists:adkats_roles,role_id',
                'user_notes' => 'max:1000',
            ]);

            if ($v->fails()) {
                return redirect()->route('admin.adkats.users.edit', [$id])->withErrors($v)->withInput();
            }

            if ($this->request->has('user_name') && $user->user_name != $username) {
                $user->user_name = $username;
            }

            if ($this->request->has('user_email') && $user->user_email != $email) {
                $user->user_email = $email;
            }

            if ($user->user_role != $roleId) {
                $user->user_role = $roleId;
            }

            if ($this->request->has('user_expiration')) {
                $user->user_expiration = Carbon::parse($expiration)->toDateTimeString();
            } else {
                $user->user_expiration = Carbon::now()->addYears(20)->toDateTimeString();
            }

            // Always save the notes field
            $user->user_notes = $notes;

            $soldier_ids = [];

            $user->soldiers()->delete();

            if ($this->request->has('soldiers')) {
                foreach ($soldiers as $soldier) {
                    $soldier_ids[] = new Soldier(['player_id' => $soldier]);
                }
            }

            if ($this->request->has('soldier')) {
                $players = Player::where('SoldierName', $this->request->get('soldier'))->pluck('PlayerID');

                foreach ($players as $player) {
                    if (! in_array($player, $soldiers)) {
                        $soldier_ids[] = new Soldier(['player_id' => $player]);
                    }
                }
            }

            if (! empty($soldier_ids)) {
                $user->soldiers()->saveMany($soldier_ids);
            }

            $user->save();

            $this->messages[] = sprintf('Changes Saved!');

            return redirect()->route('admin.adkats.users.edit', [$id])->with('messages', $this->messages);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.adkats.users.index')->withErrors([
                sprintf('User #%u doesn\'t exist.', $id),
            ]);
        }
    }

    /**
     * Delete user.
     *
     * @param int $id User ID
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
            return redirect()->route('admin.adkats.users.index')->withErrors([
                sprintf('User #%u doesn\'t exist.', $id),
            ]);
        }
    }
}
