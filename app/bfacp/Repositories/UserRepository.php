<?php namespace BFACP\Repositories;

use BFACP\Account\Role;
use BFACP\Account\Setting;
use BFACP\Account\User;
use Carbon\Carbon;

class UserRepository
{
    /**
     * Create a new user
     * @param  array   $input
     * @param  integer $role  Default role is 2
     * @return BFACP\Account\User
     */
    public function signup($input = [], $role = 2, $confirmed = false)
    {
        $user = new User;

        $user->username = array_get($input, 'username');
        $user->email    = array_get($input, 'email');
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
            $user->setting()->save(Setting::create([]));
        }

        return $user;
    }
}
