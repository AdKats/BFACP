<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Account\Role;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Http\Controllers\BaseController;
use Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use MainHelper;

class UsersController extends BaseController
{
    /**
     * Shows the user listing
     */
    public function index()
    {
        // Fetch the users and paginate
        $users = User::orderBy('username')->paginate(60);

        return View::make('admin.site.users.index', compact('users'))
            ->with('page_title', Lang::get('navigation.admin.site.items.users.title'));
    }

    /**
     * Show the editing page
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
            $roles = Cache::remember('site.roles.list', 24 * 60, function () {
                return Role::lists('name', 'id');
            });

            // Set the page title
            $page_title = Lang::get('navigation.admin.site.items.users.items.edit.title', ['id' => $id]);

            // Populate the form fields with the user information
            Former::populate($user);

            return View::make('admin.site.users.edit', compact('user', 'page_title', 'roles'));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.users.index')->withErrors([sprintf('User #%u doesn\'t exist.', $id)]);
        }
    }

    /**
     * Update user
     * @param  integer $id User ID
     */
    public function update($id)
    {
        try {

            $user = User::findOrFail($id);

            $messages = [];

            $username = trim(Input::get('username', null));
            $email    = trim(Input::get('email', null));
            $roleId   = trim(Input::get('role', null));
            $lang     = trim(Input::get('language', null));
            $status   = trim(Input::get('confirmed', null));
            $soldiers = explode(',', Input::get('soldiers', ''));

            $v = Validator::make(Input::all(), [
                'username'      => 'required|alpha_num|min:4|unique:bfacp_users,username,' . $id,
                'email'         => 'required|email|unique:bfacp_users,email,' . $id,
                'language'      => 'required|in:' . implode(',', array_keys(Config::get('bfacp.site.languages'))),
                'generate_pass' => 'boolean',
                'confirmed'     => 'boolean'
            ]);

            if ($v->fails()) {
                return Redirect::route('admin.site.users.edit', [$id])->withErrors($v)->withInput();
            }

            if (Input::has('generate_pass')) {

                // Generate a new password
                $newPassword = MainHelper::generateStrongPassword(12);

                // Send the email to the user with their new password
                Mail::send(
                    'emails.user.passwordchange',
                    compact('user', 'newPassword'),
                    function ($message) use ($user) {
                        $message
                        ->to($user->email, $user->username)
                        ->subject(Lang::get('email.password_changed.subject'));
                    }
                );

                // Change the user password
                $user->password              = $newPassword;
                $user->password_confirmation = $newPassword;

                $messages[] = Lang::get('site.admin.users.updates.password.generated', ['username' => $user->username, 'email' => $user->email]);
            }

            // Update the user role if it's been changed
            if ($roleId != $user->roles[0]->id) {
                $user->roles()->detach($user->roles[0]->id);
                $user->roles()->attach($roleId);
            }

            // Update the user language if it's been changed
            if ($lang != $user->setting->lang) {
                $user->setting()->update([
                    'lang' => $lang
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
                        unset($soldier_ids[$key]);
                    }
                }

                $user->soldiers()->saveMany($soldier_ids);
            }

            $user->save();

            return Redirect::route('admin.site.users.edit', [$id])->with('messages', $messages);

        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.users.edit', [$id])->withErrors(['Unable to complete action.']);
        }
    }

    /**
     * Delete user
     * @param  integer $id User ID
     */
    public function destroy($id)
    {
        try {
            $user     = User::findOrFail($id);
            $username = $user->username;
            $user->delete();

            return MainHelper::response([
                'url' => route('admin.site.users.index')
            ], sprintf('%s was deleted', $username));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.site.users.index')->withErrors([sprintf('User #%u doesn\'t exist.', $id)]);
        }
    }
}
