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

class Ban extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'adkats_bans';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'ban_id';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'ban_id' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Converts columns to carbon objects
     * @var array
     */
    protected $dates = array('ban_startTime', 'ban_endTime');

    /**
     * Gets the players basic information
     *
     * @return array
     */
    public function playerInfo()
    {
        return $this->hasOne( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'PlayerID', 'player_id' );
    }

    /**
     * Gets the current ban record from the records table
     *
     * @return array
     */
    public function info()
    {
        return $this->hasOne( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'record_id', 'latest_record_id' );
    }
}
