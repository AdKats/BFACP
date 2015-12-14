<?php namespace BFACP\Adkats;

use BFACP\Elegant;

class Record extends Elegant
{
    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'adkats_records_main';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'record_id';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['record_id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['record_time'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['is_web', 'stamp'];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function target()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'target_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function source()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server\Server', 'server_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function type()
    {
        return $this->belongsTo('BFACP\Adkats\Command', 'command_type')->select([
            'command_id',
            'command_active',
            'command_name',
            'command_playerInteraction',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function action()
    {
        return $this->belongsTo('BFACP\Adkats\Command', 'command_action')->select([
            'command_id',
            'command_active',
            'command_name',
            'command_playerInteraction',
        ]);
    }

    /**
     * Was record issued from the web
     *
     * @return bool
     */
    public function getIsWebAttribute()
    {
        return $this->attributes['adkats_web'] == 1;
    }

    public function getStampAttribute()
    {
        return $this->record_time->toIso8601String();
    }
}
