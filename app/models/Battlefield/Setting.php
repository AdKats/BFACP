<?php namespace ADKGamers\Webadmin\Models\Battlefield;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Illuminate\Support\Facades\Crypt;
use Eloquent;

class Setting extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'bfadmincp_settings_gameserver';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'server_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = array('server_id', 'rcon_pass_hash', 'name_strip');

    /**
     * Decrypts the password hash
     * @return string
     */
    public function getPass()
    {
        return Crypt::decrypt($this->rcon_pass_hash);
    }

    public function getPassHash($pass)
    {
        return Crypt::encrypt($pass);
    }
}
