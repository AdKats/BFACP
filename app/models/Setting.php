<?php namespace ADKGamers\Webadmin\Models;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Eloquent;

class Setting extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bfadmincp_settings';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;
}
