<?php namespace BFACP\Battlefield;

use BFACP\Elegant;

class Reputation extends Elegant
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
    protected $table = 'adkats_player_reputation';

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
    protected $guarded = ['player_id'];

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
    protected $appends = ['color'];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    public function scopeOfGame($query, $game)
    {
        return $query->whereHas('game', function ($q) use ($game) {
            return $q->where('Name', $game);
        });
    }

    /**
     * Gets the class name for the view
     *
     * @return string
     */
    public function getColorAttribute()
    {
        $color = 'text-blue';

        if ($this->total_rep_co > 100) {
            $color = 'text-green';
        } elseif ($this->total_rep_co < -100) {
            $color = 'text-red';
        } elseif ($this->total_rep_co >= -100 && $this->total_rep_co <= 100) {
            $color = 'text-yellow';
        }

        return $color;
    }

    /**
     * @param $query
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scopeMostReputable($query)
    {
        return $query->where('total_rep_co', '>=', 300)->orderBy('total_rep_co', 'desc');
    }

    /**
     * @param $query
     *
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
