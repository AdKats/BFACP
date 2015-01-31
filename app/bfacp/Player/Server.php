<?php namespace BFACP\Player;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Server extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tbl_server_player';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'StatsID';

    /**
     * Fields not allowed to be mass assigned
     * @var array
     */
    protected $guarded = ['*'];

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
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server', 'ServerID');
    }
}
