<?php namespace BFACP\Battlefield;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Chat extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tbl_chatlog';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['ID'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = ['logDate'];

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
    protected $appends = ['stamp', 'class_css'];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'logPlayerID');
    }

    /**
     * Excludes server and player chat spam from the result
     */
    public function scopeExcludeSpam($query)
    {
        return $query->whereNotNull('logPlayerID')->whereNotIn('logMessage', [
            'ID_CHAT_REQUEST_MEDIC',
            'ID_CHAT_REQUEST_AMMO',
            'ID_CHAT_THANKS',
            'ID_CHAT_REQUEST_RIDE',
            'ID_CHAT_AFFIRMATIVE',
            'ID_CHAT_GOGOGO',
            'ID_CHAT_SORRY',
            'ID_CHAT_ATTACK/DEFEND',
            'ID_CHAT_REQUEST_ORDER',
            'ID_CHAT_GET_IN',
            'ID_CHAT_NEGATIVE',
            'ID_CHAT_GET_OUT',
            'ID_CHAT_REQUEST_REPAIRS'
        ]);
    }

    public function getStampAttribute()
    {
        return $this->logDate->toIso8601String();
    }

    public function getClassCssAttribute()
    {
        switch($this->logSubset)
        {
            case "Global":
                $class = 'label bg-teal';
            break;

            case "Team":
                $class = 'label bg-blue';
            break;

            case "Squad":
                $class = 'label bg-orange';
            break;

            default:
                $class = 'label bg-yellow';
        }

        return $class;
    }
}
