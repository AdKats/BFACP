<?php namespace BFACP\Battlefield;

use BFACP\Exceptions\RconException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model AS Eloquent;
use Illuminate\Support\Facades\Crypt;

class Setting extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'bfadmincp_settings_gameserver';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'server_id';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * The attributes excluded form the models JSON response.
     * @var array
     */
    protected $hidden = ['rcon_pass_hash', 'uptime_robot_id'];

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
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server', 'server_id');
    }

    /**
     * Decypts the RCON Password
     * @return string
     */
    public function getPassword()
    {
        if(empty($this->rcon_pass_hash)) {
            throw new RconException(500, "RCON Password Not Set");
        }

        return Crypt::decrypt($this->rcon_pass_hash);
    }
}
