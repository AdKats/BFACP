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

class Reputation extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'adkats_player_reputation';

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
    protected $guarded = array( 'player_id' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;
}
