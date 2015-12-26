<?php namespace BFACP\Repositories;

use BFACP\Account\Setting;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Zizaco\Confide\Facade as Confide;

class UserRepository
{
    /**
     * Create a new user
     *
     * @param  array   $input
     * @param  integer $role        Default role is 2
     * @param  bool    $confirmed
     * @param  bool    $autoGenPass Generate a secure password if true
     * @param bool     $skipEmail   Skips the sending of the email
     *
     * @return User
     */
    public function signup($input = [], $role = 2, $confirmed = false, $autoGenPass = false, $skipEmail = false)
    {
        $user = new User();

        $user->username = array_get($input, 'username');
        $user->email = array_get($input, 'email');

        if ($autoGenPass) {
            $pass1 = $this->generatePassword();
            $pass2 = $pass1;
        } else {
            $pass1 = array_get($input, 'password');
            $pass2 = array_get($input, 'password_confirmation');
        }

        $user->password = $pass1;

        // The password confirmation will be removed from model
        // before saving. This field will be used in Ardent's
        // auto validation.
        $user->password_confirmation = $pass2;

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

            if (!$skipEmail) {
                $this->sendPasswordChangeEmail($user->username, $user->email, $pass1);
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

    /**
     * Generates a strong password
     *
     * @param  integer $len Length of generated password
     *
     * @return string
     */
    public function generatePassword($len = 12)
    {
        $pass = MainHelper::generateStrongPassword($len);

        return $pass;
    }

    /**
     * @param  string $username
     * @param  string $email
     * @param  string $password
     *
     * @return null
     */
    public function sendPasswordChangeEmail($username, $email, $newPassword)
    {
        // Send the email to the user with their new password
        Mail::send('emails.user.passwordchange', compact('username', 'newPassword'),
            function ($message) use ($username, $email) {
                $message->to($email, $username)
                    ->subject(Lang::get('email.password_changed.subject'));
            });
    }
}
