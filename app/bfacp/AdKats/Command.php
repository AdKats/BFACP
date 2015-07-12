<?php namespace BFACP\AdKats;

use BFACP\Elegant;

class Command extends Elegant
{
    /**
     * Should model handle timestamps
     * @var boolean
     */
    public $timestamps = false;

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
     * Returns the commands that usable by the guest
     *
     * @param $query


*
*@return
     */
    public function scopeGuest($query)
    {
        return $query->where('command_playerInteraction', false);
    }

    /**
     * Returns the commands that usable by the admin

*
* @param $query


*
*@return
     */
    public function scopeAdmin($query)
    {
        return $query->where('command_playerInteraction', true);
    }

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
