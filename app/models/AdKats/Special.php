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

class Special extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_specialplayers';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'specialplayer_id';

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
    protected $dates = array( 'player_effective', 'player_expiration' );

    public function player()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'PlayerID', 'player_id');
    }

    public function server()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\Battlefield\\Server', 'ServerID', 'player_server');
    }
}
