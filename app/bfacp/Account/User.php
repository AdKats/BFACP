<?php namespace BFACP\Account;

use BFACP\Elegant;
use Illuminate\Support\Facades\Config;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Elegant implements ConfideUserInterface
{
    use ConfideUser;
    use HasRole;

    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_users';

    /**
     * Table primary key
     * @var string
     */
    //protected $primaryKey = '';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = ['lastseen_at'];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'confirmation_code'];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = ['gravatar'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['setting', 'roles'];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'username'              => 'required|unique:bfacp_users,username|alpha_num',
        'email'                 => 'required|unique:bfacp_users,email|email',
        'password'              => 'required|between:8,32|confirmed',
        'password_confirmation' => 'required_with:password|between:8,32'
    ];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Get the remember token for the user
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the remember token for the user
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany('BFACP\Account\Role', Config::get('entrust::assigned_roles_table'));
    }

    public function setting()
    {
        return $this->hasOne('BFACP\Account\Setting', 'user_id');
    }

    public function getConfirmedAttribute()
    {
        return $this->attributes['confirmed'] == 1;
    }

    /**
     * Gets users gravatar image
     * @return string
     */
    public function getGravatarAttribute()
    {
        $email = $this->setting->gravatar ?: $this->email;
        $url   = '//www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?s=80&d=mm&r=x';

        return $url;
    }
}
