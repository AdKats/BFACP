<?php namespace BFACP\Repositories;

use BFACP\Account\Setting;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Zizaco\Confide\Facade as Confide;

class UserRepository
{
    /**
     * Create a new user
     *
     * @param  array   $input
     * @param  integer $role Default role is 2
     * @param bool     $confirmed
     *
     * @return User
     */
    public function signup($input = [], $role = 2, $confirmed = false)
    {
        $user = new User();

        $user->username = array_get($input, 'username');
        $user->email = array_get($input, 'email');
        $user->password = array_get($input, 'password');

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $user->password_confirmation = array_get($input, 'password_confirmation');

        // Generate a random confirmation code
        $user->confirmation_code = md5(uniqid(mt_rand(), true));

        // Update last seen timestamp
        $user->lastseen_at = Carbon::now();

        $user->confirmed = $confirmed;

        // Save if valid. Password field will be hashed before save
        $user->save();

        if (!is_null($user->id)) {
            $user->roles()->attach($role);
            $user->setting()->save(new Setting([
                'lang' => array_get($input, 'lang', 'en'),
            ]));

            if (!empty(array_get($input, 'ign'))) {
                $players = Player::where('SoldierName', array_get($input, 'ign'))->lists('PlayerID');

                foreach ($players as $player) {

                    // Check if an existing user already has claimed the player
                    // and if so do not associate with the new account.
                    if (Soldier::where('player_id', $player)->count() == 0) {
                        $soldier = new Soldier(['player_id' => $player]);
                        $soldier->user()->associate($user)->save();
                    }
                }
            }
        }

        return $user;
    }

    /**
     * Attempts to login with the given credentials.
     *
     * @param  array $input
     *
     * @return boolean
     */
    public function login($input)
    {
        if (!isset($input['password'])) {
            $input['password'] = null;
        }

        return Confide::logAttempt($input, Config::get('confide::signup_confirm'));
    }

    /**
     * Log out the user
     *
     * @return null
     */
    public function logout()
    {
        return Confide::logout();
    }

    /**
     * Checks if the credentials has been throttled by too
     * many failed login attempts
     *
     * @param  array $input
     *
     * @return boolean
     */
    public function isThrottled($input)
    {
        return Confide::isThrottled($input);
    }

    /**
     * Checks if the given credentials correponds to a user
     * that exists but is not confirmed
     *
     * @param  array $input
     *
     * @return boolean
     */
    public function existsButNotConfirmed($input)
    {
        $user = Confide::getUserByEmailOrUsername($input);

        if ($user) {
            $correctPassword = Hash::check(isset($input['password']) ? $input['password'] : false, $user->password);

            return !$user->confirmed && $correctPassword;
        }

        return false;
    }

    /**
     * Resets the password of a user. The $input['token'] will tell which user.
     *
     * @param  array $input Array containing 'token', 'password' and 'password_confirmation' keys.
     *
     * @return boolean
     */
    public function resetPassword($input)
    {
        $result = false;
        $user = Confide::userByResetPasswordToken($input['token']);

        if ($user) {
            $user->password = $input['password'];
            $user->password_confirmation = $input['password_confirmation'];
            $result = $this->save($user);
        }

        // If result is positive, destroy token
        if ($result) {
            Confide::destroyForgotPasswordToken($input['token']);
        }

        return $result;
    }

    /**
     * Attempt to confirm the account with code
     *
     * @param  string $code
     *
     * @return boolean
     */
    public function confirm($code)
    {
        $user = User::where('confirmation_code', $code)->first();

        if (!$user['confirmed']) {
            return Confide::confirm($code);
        }

        return false;
    }
}
