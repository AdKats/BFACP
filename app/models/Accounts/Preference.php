<?php

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

class Preference extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bfadmincp_user_preferences';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = array('user_id', 'timezone', 'report_notify_alert', 'report_notify_sound', 'report_notify_sound_file', 'bf3_playerid', 'bf4_playerid');

    /**
     * Gets the players basic information
     *
     * @return array
     */
    public function bf3player()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'bf3_playerid', 'PlayerID' );
    }

    /**
     * Gets the players basic information
     *
     * @return array
     */
    public function bf4player()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'bf4_playerid', 'PlayerID' );
    }
}
