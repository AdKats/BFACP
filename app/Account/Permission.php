<?php

namespace BFACP\Account;

use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustPermission;

/**
 * Class Permission.
 */
class Permission extends EntrustPermission
{
    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:bfacp_permissions,name|between:3,255',
    ];

    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['id'];

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

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = Config::get('entrust.permissions_table');
    }
}
