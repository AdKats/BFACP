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

class Battlelog extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'bfadmincp_battlelog_playerdata';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'id' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Gets the players basic information
     *
     * @return array
     */
    public function player()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'player_id', 'PlayerID' );
    }
}
