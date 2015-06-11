<?php namespace BFACP\Account;

use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfacp_roles';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = [];

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
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:bfacp_roles,name|between:4,255'
    ];

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function permissions()
    {
        return $this->belongsToMany('BFACP\Account\Permission', Config::get('entrust::permission_role_table'));
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function users()
    {
        return $this->belongsToMany('BFACP\Account\User', Config::get('entrust::assigned_roles_table'));
    }
}
