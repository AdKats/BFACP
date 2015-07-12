<?php namespace BFACP\Account;

use BFACP\Elegant;
use Illuminate\Database\Eloquent\Model;

class Soldier extends Elegant
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
    protected $table = 'bfacp_users_soldiers';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return Model
     */
    public function user()
    {
        return $this->belongsTo('BFACP\Account\User', 'user_id');
    }

    /**
     * @return Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'player_id');
    }
}
