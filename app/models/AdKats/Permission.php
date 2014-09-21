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

class Permission extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_rolecommands';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = array('role_id', 'command_id');

    public function command()
    {
        return $this->belongsTo( 'ADKGamers\\Webadmin\\Models\\AdKats\\Command');
    }
}
