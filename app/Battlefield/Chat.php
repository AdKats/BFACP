<?php namespace BFACP\Battlefield;

use BFACP\Elegant;

class Chat extends Elegant
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
    protected $table = 'tbl_chatlog';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['ID'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['logDate'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['stamp', 'class_css', 'profile_url'];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server\Server', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'logPlayerID');
    }

    /**
     * Gets the URL to the players profile
     *
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return !is_null($this->logPlayerID) ? route('player.show', [
            'id'   => $this->logPlayerID,
            'name' => $this->logSoldierName,
        ]) : null;
    }

    /**
     * Excludes server and player chat spam from the result
     *
     * @param $query
     *
     * @return
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
            'ID_CHAT_REQUEST_REPAIRS',
        ]);
    }

    /**
     * Converts the chat timestamp to an ISO 8601 stamp
     *
     * @return string
     */
    public function getStampAttribute()
    {
        return $this->logDate->toIso8601String();
    }

    /**
     * Returns the class that should be applied based on the chat visibility
     *
     * @return string
     */
    public function getClassCssAttribute()
    {
        switch ($this->logSubset) {
            case 'Global':
                $class = 'label bg-teal';
                break;

            case 'Team':
                $class = 'label bg-blue';
                break;

            case 'Squad':
                $class = 'label bg-green';
                break;

            default:
                $class = 'label bg-yellow';
        }

        return $class;
    }
}
