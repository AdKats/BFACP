<?php namespace BFACP\AdKats;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Battlelog extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_battlelog_players';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['persona_id', 'gravatar', 'persona_banned', 'user_id'];

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
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'player_id');
    }
}
