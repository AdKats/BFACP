<?php namespace ADKGamers\Webadmin\Models\Battlefield;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Eloquent;
use Carbon\Carbon;

class Chatlog extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'tbl_chatlog';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'ID', 'logPlayerID' );

    /**
     * Converts columns to carbon objects
     * @var array
     */
    protected $dates = array( 'logDate' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    public function server()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\Battlefield\\Server', 'ServerID', 'ServerID');
    }
}
