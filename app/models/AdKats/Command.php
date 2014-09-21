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

class Command extends Eloquent
{
    /**
     * Table model should use
     * @var string
     */
    protected $table = 'adkats_commands';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'command_id';

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Returns the name of the command
     * @return string
     */
    public function name()
    {
        return $this->command_name;
    }

    /**
     * Returns the key of the command
     * @return string
     */
    public function key()
    {
        return $this->command_key;
    }
}
