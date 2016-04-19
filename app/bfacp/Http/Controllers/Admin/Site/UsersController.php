<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Account\Role;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use Former\Facades\Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UsersController extends BaseController
{
    /**
     * Shows the user listing
     */
    public function index()
    {
        // Fetch the users and paginate
        $users = User::orderBy('username')->paginate(60);

        return View::make('admin.site.users.index', compact('users'))->with('page_title',
            Lang::get('navigation.admin.site.items.users.title'));
    }

    public function create()
    {
        // Get the list of roles
        $roles = Role::lists('name', 'id');

        return View::make('admin.site.users.create', compact('roles'))->with('page_title',
            Lang::get('navigation.admin.site.items.users.items.create.title'));
    }

    public function store()
    {
        $repo = app('BFACP\Repositories\UserRepository');

        $v = Validator::make(Input::all(), [
            'username' => 'required|alpha_dash|min:4|unique:bfacp_users,username',
            'email'    => 'required|email|unique:bfacp_users,email',
            'language' => 'required|in:' . implode(',', array_keys(Config::get('bfacp.site.languages'))),
            'soldier'  => 'exists:tbl_playerdata,SoldierName',
            'role'     => 'required',
            'language' => 'required',
        ]);

        if ($v->fails()) {
            return Redirect::route('admin.site.users.create')->withErrors($v)->withInput();
        }

        $data = [
            'username' => Input::get('username'),
            'email'    => Input::get('email'),
            'ign'      => Input::get('soldier'),
            'lang'     => Input::get('language', 'en'),
        ];

        $user = $repo->signup($data, Input::get('role', 2), true, true);

        $this->messages[] = Lang::get('site.admin.users.updates.password.generated',
            ['username' => $user->username, 'email' => $user->email]);

        return Redirect::route('admin.site.users.edit', [$user->id])->withMessages($this->messages);
    }

    /**
     * Show the editing page
     *
     * @param integer $id User ID
     */
    public function edit($id)
    {
        try {
            // If the user we are editing is the current logged in user don't refetch them.
            if ($this->isLoggedIn && $this->user->id == $id) {
                $user = $this->user;
            } else {
                $user = User::findOrFail($id);
            }

            // Get the list of roles
            $roles = Role::lists('name', 'id');

            // Set the page title
            $page_title = Lang::get('navigation.admin.site.items.users.items.edit.title', ['id' => $id]);

            // Populate the form fields with the user information
            Former::populate($user);

            return View::make('admin.site.users.edit', compact('user', 'page_title', 'roles'));
        } catch (ModelNotFoundException $e) {
            $this->messages[] = Lang::get('alerts.user.invlid', ['userid' => $id]);

            return Redirect::route('admin.site.users.index')->withErrors($this->messages);
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

            $username = trim(Input::get('username', null));
            $email = trim(Input::get('email', null));
            $roleId = trim(Input::get('role', null));
            $lang = trim(Input::get('language', null));
            $status = trim(Input::get('confirmed', null));
            $soldiers = explode(',', Input::get('soldiers', ''));

            $v = Validator::make(Input::all(), [
                'username'      => 'required|alpha_dash|min:4|unique:bfacp_users,username,' . $id,
                'email'         => 'required|email|unique:bfacp_users,email,' . $id,
                'language'      => 'required|in:' . implode(',', array_keys(Config::get('bfacp.site.languages'))),
                'generate_pass' => 'boolean',
                'confirmed'     => 'boolean',
            ]);

            if ($v->fails()) {
                return Redirect::route('admin.site.users.edit', [$id])->withErrors($v)->withInput();
            }

            // Update the user role if it's been changed
            if ($roleId != $user->roles[0]->id) {
                $user->roles()->detach($user->roles[0]->id);
                $user->roles()->attach($roleId);
            }

            // Update the user language if it's been changed
            if ($lang != $user->setting->lang) {
                $user->setting()->update([
                    'lang' => $lang,
                ]);
            }

            // Update account stats
            if ($status != $user->confirmed) {
                $user->confirmed = $status;
            }

            // Update username
            if ($username != $user->username) {
                $user->username = $username;
            }

            // Update email
            if ($email != $user->email) {
                $user->email = $email;
            }

            if (Input::has('generate_pass')) {

                $repo = app('BFACP\Repositories\UserRepository');

                // Generate a new password
                $newPassword = $repo->generatePassword();

                $repo->sendPasswordChangeEmail($user->username, $user->email, $newPassword);

                // Change the user password
                $user->password = $newPassword;
                $user->password_confirmation = $newPassword;

                $this->messages[] = Lang::get('site.admin.users.updates.password.generated',
                    ['username' => $user->username, 'email' => $user->email]);
            }

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

                foreach ($soldier_ids as $key => $soldier) {
                    // Check if an existing user already has claimed the player
                    // and if so do not associate with the account.
                    if (Soldier::where('player_id', $soldier->player_id)->count() == 1) {
                        $this->messages[] = Lang::get('alerts.user.soldier_taken', ['playerid' => $soldier->player_id]);
                        unset($soldier_ids[ $key ]);
                    }
                }

                $user->soldiers()->saveMany($soldier_ids);
            }

            $user->save();

            return Redirect::route('admin.site.users.edit', [$id])->withMessages($this->messages);
        } catch (ModelNotFoundException $e) {
            $this->messages[] = Lang::get('alerts.user.invlid', ['userid' => $id]);

            return Redirect::route('admin.site.users.edit', [$id])->withErrors($this->messages);
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
            $username = $user->username;
            $user->delete();

            return MainHelper::response([
                'url' => route('admin.site.users.index'),
            ], Lang::get('alerts.user.deleted', compact('username')));
        } catch (ModelNotFoundException $e) {
            $this->messages[] = Lang::get('alerts.user.invlid', ['userid' => $id]);

            return Redirect::route('admin.site.users.index')->withErrors($this->messages);
        }
    }
}
