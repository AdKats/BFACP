<?php namespace BFACP\Account;

class Preference extends \Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfadmincp_user_preferences';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Date fields to convert to carbon instances
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = [];

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = TRUE;

    /**
     * Append custom attributes to output
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automaticly
     * @var array
     */
    protected $with = ['bf3player', 'bf4player'];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'gravatar'     => 'email',
        'timezone'     => 'timezone',
        'bf3_playerid' => 'exists:tbl_playerdata,PlayerID',
        'bf4_playerid' => 'exists:tbl_playerdata,PlayerID'
    ];

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('BFACP\Account\User', 'user_id');
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function bf3player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'bf3_playerid')->remember(10);
    }

    /**
     * @return Illuminate\Database\Eloquent\Model
     */
    public function bf4player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'bf4_playerid')->remember(10);
    }
}
