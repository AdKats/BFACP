<?php namespace ADKGamers\Webadmin\Models\AdKats;

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
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_settings';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'server_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;
}
