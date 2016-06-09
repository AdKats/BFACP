<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Account\Role;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use BFACP\Repositories\UserRepository;
use Former\Facades\Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersController.
 */
class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('permission:admin.site.users');
    }

    /**
     * Shows the user listing.
     */
    public function index()
    {
        // Fetch the users and paginate
        $users = User::orderBy('username')->paginate(60);

        return view('admin.site.users.index', compact('users'))->with('page_title',
            trans('navigation.admin.site.items.users.title'));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        // Get the list of roles
        $roles = Role::lists('name', 'id');

        return view('admin.site.users.create', compact('roles'))->with('page_title',
            trans('navigation.admin.site.items.users.items.create.title'));
    }

    /**
     * @return mixed
     */
    public function store()
    {
        $repo = app(UserRepository::class);

        $v = Validator::make($this->request->all(), [
            'username' => 'required|alpha_dash|min:4|unique:bfacp_users,username',
            'email'    => 'required|email|unique:bfacp_users,email',
            'language' => 'required|in:'.implode(',', array_keys($this->config->get('bfacp.site.languages'))),
            'soldier'  => 'exists:tbl_playerdata,SoldierName',
            'role'     => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('admin.site.users.create')->withErrors($v)->withInput();
        }

        $data = [
            'username' => $this->request->get('username'),
            'email'    => $this->request->get('email'),
            'ign'      => $this->request->get('soldier'),
            'lang'     => $this->request->get('language', 'en'),
        ];

        $user = $repo->signup($data, $this->request->get('role', 2), true);

        $this->messages[] = trans('site.admin.users.updates.password.generated',
            ['username' => $user->username, 'email' => $user->email]);

        $this->log->info(sprintf('%s created user "%s".', $this->user->username, $user->username), $user->toArray());

        return redirect()->route('admin.site.users.edit', [$user->id])->withMessages($this->messages);
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
            // If the user we are editing is the current logged in user don't re-fetch them.
            if ($this->isLoggedIn && $this->user->id == $id) {
                $user = $this->user;
            } else {
                $user = User::findOrFail($id);
            }

            /*
             * If the user doesn't have a role assigned we need to re-create the link.
             */
            if (! isset($user->roles[0])) {

                // Fetch the registered role
                $registeredUserRole = Role::where('name', 'Registered')->first();

                // Associate the registered role with the user
                $user->roles()->attach($registeredUserRole->id);

                // Save the new user information
                $user->save();

                // Reload the roles relationship
                $user->load('roles');
            }

            // Get the list of roles
            $roles = Role::lists('name', 'id');

            // Set the page title
            $page_title = trans('navigation.admin.site.items.users.items.edit.title', ['id' => $id]);

            // Populate the form fields with the user information
            Former::populate($user);

            return view('admin.site.users.edit', compact('user', 'page_title', 'roles'));
        } catch (ModelNotFoundException $e) {
            $this->messages[] = trans('alerts.user.invalid', ['userid' => $id]);

            return redirect()->route('admin.site.users.index')->withErrors($this->messages);
        }
    }

    /**
     * Update user.
     *
     * @param int $id User ID
     *
     * @return $this
     */
    public function update($id)
    {
        try {
            $user = User::findOrFail($id);

            $username = trim($this->request->get('username', null));
            $email = trim($this->request->get('email', null));
            $roleId = trim($this->request->get('role', null));
            $lang = trim($this->request->get('language', null));
            $status = (bool) trim($this->request->get('account_status', false));
            $soldiers = explode(',', $this->request->get('soldiers', ''));

            $v = Validator::make($this->request->all(), [
                'username'      => 'required|alpha_dash|min:4|unique:bfacp_users,username,'.$id,
                'email'         => 'required|email|unique:bfacp_users,email,'.$id,
                'language'      => 'required|in:'.implode(',', array_keys($this->config->get('bfacp.site.languages'))),
                'generate_pass' => 'boolean',
            ]);

            if ($v->fails()) {
                return redirect()->route('admin.site.users.edit', [$id])->withErrors($v)->withInput();
            }

            // Update the user role if it's been changed
            if ($roleId != $user->roles[0]->id) {
                if (count($user->roles) > 1) {
                    $user->roles()->sync([]);
                } else {
                    $user->roles()->detach($user->roles[0]->id);
                }

                $user->roles()->attach($roleId);
                $role = Role::find($roleId);
                $this->log->info(sprintf('%s changed %s role to %s.', $this->user->username, $user->username,
                    $role->name));
            }

            // Update the user language if it's been changed
            if ($lang != $user->setting->lang) {
                $user->setting()->update([
                    'lang' => $lang,
                ]);
                $languages = $this->config->get('bfacp.site.languages');
                $this->log->info(sprintf('%s changed %s language to %s.', $this->user->username, $user->username,
                    $languages[$lang]));
            }

            // Update username
            if ($username != $user->username) {
                $this->log->info(sprintf('%s changed %s username to %s.', $this->user->username, $user->username,
                    $username));
                $user->username = $username;
            }

            // Update email
            if ($email != $user->email) {
                $this->log->info(sprintf('%s changed %s email to %s.', $this->user->username, $user->username, $email));
                $user->email = $email;
            }

            if ($this->request->has('generate_pass')) {
                $repo = app(UserRepository::class);

                // Generate a new password
                $newPassword = $repo->generatePassword();

                $repo->sendPasswordChangeEmail($user->username, $user->email, $newPassword);

                // Change the user password
                $user->password = $newPassword;

                $this->messages[] = trans('site.admin.users.updates.password.generated',
                    ['username' => $user->username, 'email' => $user->email]);

                $this->log->info(sprintf('%s changed %s password.', $this->user->username, $user->username));
            }

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
                foreach ($soldier_ids as $key => $soldier) {
                    // Check if an existing user already has claimed the player
                    // and if so do not associate with the account.
                    if (Soldier::where('player_id', $soldier->player_id)->count() == 1) {
                        $this->errors[] = trans('alerts.user.soldier_taken', ['playerid' => $soldier->player_id]);
                        unset($soldier_ids[$key]);
                    }
                }

                $user->soldiers()->saveMany($soldier_ids);
            }

            $user->save();

            $this->messages[] = trans('alerts.user.saved');

            return redirect()->route('admin.site.users.edit',
                [$id])->withMessages($this->messages)->withErrors($this->errors);
        } catch (ModelNotFoundException $e) {
            $this->messages[] = trans('alerts.user.invalid', ['userid' => $id]);

            return redirect()->route('admin.site.users.edit', [$id])->withErrors($this->messages);
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
            $username = $user->username;
            $user->delete();

            $this->messages[] = trans('alerts.user.deleted', compact('username'));

            $this->log->info(sprintf('%s deleted user %s.', $this->user->username, $username));

            return MainHelper::response([
                'url'      => route('admin.site.users.index'),
                'messages' => $this->messages,
            ]);
        } catch (ModelNotFoundException $e) {
            $this->errors[] = trans('alerts.user.invalid', ['userid' => $id]);

            return redirect()->route('admin.site.users.index')->withErrors($this->errors);
        }
    }
}
