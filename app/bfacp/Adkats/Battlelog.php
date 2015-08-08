<?php namespace BFACP\Adkats;

use BFACP\Elegant;
use BFACP\Facades\Main as MainHelper;

class Battlelog extends Elegant
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
    protected $table = 'adkats_battlelog_players';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Fields allowed to be mass assigned
     *
     * @var array
     */
    protected $fillable = ['persona_id', 'gravatar', 'persona_banned', 'user_id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['gravatar_img'];

    /**
     * Models to be loaded automatically
     *
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

    /**
     * Gets users gravatar image
     *
     * @return string
     */
    public function getGravatarImgAttribute()
    {
        return MainHelper::gravatar(null, $this->gravatar, 128);
    }
}
