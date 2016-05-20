<?php

namespace BFACP\Adkats;

use BFACP\Elegant;

/**
 * Class Record.
 *
 * @property int      record_id
 * @property int      server_id
 * @property int      command_type
 * @property int      command_action
 * @property int      command_numeric
 * @property string   target_name
 * @property int|null target_id
 * @property string   source_name
 * @property int|null source_id
 * @property string   record_message
 * @property string   record_time
 * @property string   adkats_read
 * @property bool     adkats_web
 */
class Record extends Elegant
{
    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'adkats_records_main';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'record_id';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['record_id'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['record_time'];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = ['is_web', 'stamp'];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function target()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'target_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(\BFACP\Battlefield\Server\Server::class, 'server_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(\BFACP\Adkats\Command::class, 'command_type')->select([
            'command_id',
            'command_active',
            'command_name',
            'command_playerInteraction',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo(\BFACP\Adkats\Command::class, 'command_action')->select([
            'command_id',
            'command_active',
            'command_name',
            'command_playerInteraction',
        ]);
    }

    /**
     * Was record issued from the web.
     *
     * @return bool
     */
    public function getIsWebAttribute()
    {
        return $this->attributes['adkats_web'] == 1;
    }

    /**
     * @return mixed
     */
    public function getStampAttribute()
    {
        return $this->record_time->toIso8601String();
    }
}
