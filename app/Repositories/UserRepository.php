<?php

namespace BFACP\Repositories;

use BFACP\Account\Setting;
use BFACP\Account\Soldier;
use BFACP\Account\User;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Class UserRepository.
 */
class UserRepository
{
    /**
     * Create a new user.
     *
     * @param array $input
     * @param int   $role        Default role is 2
     * @param bool  $autoGenPass Generate a secure password if true
     * @param bool  $skipEmail   Skips the sending of the email
     *
     * @return User
     */
    public function signup($input = [], $role = 2, $autoGenPass = false, $skipEmail = false)
    {
        $user = new User();

        $user->username = array_get($input, 'username');
        $user->email = array_get($input, 'email');

        if ($autoGenPass) {
            $pass = $this->generatePassword();
        } else {
            $pass = array_get($input, 'password');
        }

        $user->password = $pass;

        // Update last seen timestamp
        $user->lastseen_at = Carbon::now();

        // Save if valid. Password field will be hashed before save
        $user->save();

        if (! is_null($user->id)) {
            $user->roles()->attach($role);
            $user->setting()->save(new Setting([
                'lang' => array_get($input, 'lang', 'en'),
            ]));

            if (! empty(array_get($input, 'ign'))) {
                $players = Player::where('SoldierName', array_get($input, 'ign'))->get();

                $soldiersTaken = [];

                foreach ($players as $player) {

                    // Check if an existing user already has claimed the player
                    // and if so do not associate with the new account.
                    if (Soldier::where('player_id', $player->PlayerID)->count() == 0) {
                        $soldier = new Soldier(['player_id' => $player->PlayerID]);
                        $soldier->user()->associate($user)->save();
                    } else {
                        $soldiersTaken[] = trans('alerts.user.soldier_taken', ['playerid' => $player->PlayerID]);
                    }
                }

                if (! empty($soldiersTaken)) {
                    session()->flash('warnings', $soldiersTaken);
                }
            }

            if (! $skipEmail) {
                $this->sendPasswordChangeEmail($user->username, $user->email, $pass);
            }
        }

        return $user;
    }

    /**
     * Attempts to login with the given credentials.
     *
     * @param array $input
     *
     * @return bool
     */
    public function login($input)
    {
        if (! isset($input['password'])) {
            $input['password'] = null;
        }

        return Auth::attempt($input);
    }

    /**
     * Log out the user.
     *
     * @return null
     */
    public function logout()
    {
        return Auth::logout();
    }

    /**
     * Checks if the credentials has been throttled by too
     * many failed login attempts.
     *
     * @param array $input
     *
     * @return bool
     */
    public function isThrottled($input)
    {
        return Confide::isThrottled($input);
    }

    /**
     * Checks if the given credentials correponds to a user
     * that exists but is not confirmed.
     *
     * @param array $input
     *
     * @return bool
     */
    public function existsButNotConfirmed($input)
    {
        $user = Confide::getUserByEmailOrUsername($input);

        if ($user) {
            $correctPassword = Hash::check(isset($input['password']) ? $input['password'] : false, $user->password);

            return ! $user->confirmed && $correctPassword;
        }

        return false;
    }

    /**
     * Resets the password of a user. The $input['token'] will tell which user.
     *
     * @param array $input Array containing 'token', 'password' and 'password_confirmation' keys.
     *
     * @return bool
     */
    public function resetPassword($input)
    {
        $result = false;
        $user = Auth::userByResetPasswordToken($input['token']);

        if ($user) {
            $user->password = $input['password'];
            $user->password_confirmation = $input['password_confirmation'];
            $result = $this->save($user);
        }

        // If result is positive, destroy token
        if ($result) {
            Auth::destroyForgotPasswordToken($input['token']);
        }

        return $result;
    }

    /**
     * Attempt to confirm the account with code.
     *
     * @param string $code
     *
     * @return bool
     */
    public function confirm($code)
    {
        $user = User::where('confirmation_code', $code)->first();

        if (! $user['confirmed']) {
            return Confide::confirm($code);
        }

        return false;
    }

    /**
     * Generates a strong password.
     *
     * @param int $len Length of generated password
     *
     * @return string
     */
    public function generatePassword($len = 12)
    {
        $pass = MainHelper::generateStrongPassword($len);

        return $pass;
    }

    /**
     * @param string $username
     * @param string $email
     * @param        $newPassword
     *
     * @return null
     * @internal param string $password
     */
    public function sendPasswordChangeEmail($username, $email, $newPassword)
    {
        // Send the email to the user with their new password
        Mail::send('emails.user.passwordchange', compact('username', 'newPassword'),
            function ($message) use ($username, $email) {
                $message->to($email, $username)->subject(trans('email.password_changed.subject'));
            });
    }
}
