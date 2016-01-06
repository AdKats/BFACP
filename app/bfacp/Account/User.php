<?php namespace BFACP\Account;

use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config as Config;
use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Model implements ConfideUserInterface
{
    use ConfideUser;
    use HasRole;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'username' => 'required|unique:bfacp_users,username|alpha_dash|min:4',
        'email'    => 'required|unique:bfacp_users,email|email',
        'password' => 'required|min:8|confirmed',
    ];

    /**
     * Table primary key
     *
     * @var string
     */
    //protected $primaryKey = '';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'bfacp_users';

    /**
     * Fields allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['lastseen_at'];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['gravatar', 'stamp'];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = ['setting', 'roles', 'soldiers'];

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
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the remember token for the user
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Returns the name of the remember token
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * @return Model
     */
    public function roles()
    {
        return $this->belongsToMany('BFACP\Account\Role', Config::get('entrust::assigned_roles_table'));
    }

    /**
     * @return Model
     */
    public function setting()
    {
        return $this->hasOne('BFACP\Account\Setting', 'user_id');
    }

    /**
     * @return Model
     */
    public function soldiers()
    {
        return $this->hasMany('BFACP\Account\Soldier', 'user_id');
    }

    /**
     * Has user confirmed their account
     *
     * @return boolean
     */
    public function getConfirmedAttribute()
    {
        return $this->attributes['confirmed'] == 1;
    }

    public function getStampAttribute()
    {
        if ($this->created_at instanceof Carbon) {
            return $this->created_at->toIso8601String();
        }

        return $this->created_at;
    }

    /**
     * Gets users gravatar image
     *
     * @return string
     */
    public function getGravatarAttribute()
    {
        return MainHelper::gravatar($this->email);
    }
}
