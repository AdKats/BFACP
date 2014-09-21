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
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommand;

class Record extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'adkats_records_main';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'record_id';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'record_id' );

    /**
     * Converts columns to carbon objects
     * @var array
     */
    protected $dates = array( 'record_time' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    public function server()
    {
        return $this->hasOne( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Server', 'ServerID', 'server_id' );
    }

    public function cmdtype()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\AdKats\\Command', 'command_type', 'command_id' );
    }

    public function cmdaction()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\AdKats\\Command', 'command_action', 'command_id' );
    }
}
