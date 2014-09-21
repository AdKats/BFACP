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

class Soldier extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_usersoldiers';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Returns the user
     * @return object
     */
    public function user()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\AdKats\\User', 'user_id', 'user_id');
    }

    public function player()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\Battlefield\\Player', 'PlayerID', 'player_id');
    }
}
