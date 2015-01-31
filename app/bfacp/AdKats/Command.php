<?php namespace BFACP\AdKats;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Command extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_commands';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'command_id';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = [];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = ['is_interactive', 'is_enabled', 'is_invisible'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * Command can be interacted with by player
     * @return bool
     */
    public function getIsInteractiveAttribute()
    {
        return $this->attributes['command_playerInteraction'] == 1;
    }

    /**
     * Command is enabled
     * @return bool
     */
    public function getIsEnabledAttribute()
    {
        return $this->attributes['command_active'] == 'Active';
    }

    /**
     * Command is invisible
     * @return bool
     */
    public function getIsInvisibleAttribute()
    {
        return $this->attributes['command_active'] == 'Invisible';
    }
}
