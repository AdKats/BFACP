<?php namespace BFACP\AdKats;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Ban extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_bans';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['ban_id', 'ban_sync'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = ['ban_startTime', 'ban_endTime'];

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
    protected $appends = ['is_active', 'is_expired', 'enforced_by_name', 'enforced_by_guid', 'enforced_by_ip'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function record()
    {
        return $this->belongsTo('BFACP\AdKats\Record', 'latest_record_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function previous()
    {
        return $this->hasMany('BFACP\AdKats\Record', 'target_id')
            ->whereIn('command_action', [7,8,72,73]);
    }

    /**
     * Is ban enforced by name
     * @return bool
     */
    public function getEnforcedByNameAttribute()
    {
        return $this->attributes['ban_enforceName'] == 'Y';
    }

    /**
     * IS ban enforced by guid
     * @return bool
     */
    public function getEnforcedByGuidAttribute()
    {
        return $this->attributes['ban_enforceGUID'] == 'Y';
    }

    /**
     * Is ban enforced by ip
     * @return bool
     */
    public function getEnforcedByIpAttribute()
    {
        return $this->attributes['ban_enforceIP'] == 'Y';
    }

    /**
     * Is ban active
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->attributes['ban_status'] == 'Active';
    }

    /**
     * Is ban expired
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        return $this->attributes['ban_status'] == 'Expired';
    }
}
