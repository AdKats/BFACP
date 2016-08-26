<?php

namespace BFACP\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;

/**
 * Class Role.
 */
class Role extends EntrustRole
{
    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:bfacp_roles,name|between:4,255',
    ];

    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    public function __construct()
    {
        parent::__construct();

        $this->table = Config::get('entrust.roles_table');
    }

    /**
     * @return Model
     */
    public function permissions()
    {
        return $this->belongsToMany(\BFACP\Account\Permission::class, Config::get('entrust.permission_role_table'));
    }

    /**
     * @return Model
     */
    public function users()
    {
        return $this->belongsToMany(\BFACP\Account\User::class, Config::get('entrust.assigned_roles_table'));
    }
}
