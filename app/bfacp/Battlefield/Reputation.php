<?php namespace BFACP\Battlefield;

use Illuminate\Database\Eloquent\Model AS Eloquent;
use Carbon\Carbon;

class Reputation extends Eloquent
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'adkats_player_reputation';

    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'player_id';

    /**
     * Fields allowed to be mass assigned
     * @var array
     */
    protected $fillable = ['*'];

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

    public function scopeOfGame($query, $game)
    {
        return $query->whereHas('game', function($q) use($game) {
            return $q->where('Name', $game);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeMostReputable($query)
    {
        return $query->where('total_rep_co', '>=', 300)->orderBy('total_rep_co', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeLeastReputable($query)
    {
        return $query->where('total_rep_co', '<=', -300)->orderBy('total_rep_co');
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
    public function game()
    {
        return $this->belongsTo('BFACP\Battlefield\Game', 'game_id');
    }
}
