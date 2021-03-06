<?php

namespace BFACP\Adkats;

use BFACP\Elegant;
use BFACP\Facades\Main as MainHelper;

/**
 * Class Special.
 */
class Special extends Elegant
{
    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'adkats_specialplayers';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'specialplayer_id';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['specialplayer_id'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = ['player_effective', 'player_expiration'];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = ['effective_stamp', 'expiration_stamp', 'group'];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function game()
    {
        return $this->belongsTo(\BFACP\Battlefield\Game::class, 'player_game');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo(\BFACP\Battlefield\Server\Server::class, 'server_id');
    }

    /**
     * @return mixed
     */
    public function getEffectiveStampAttribute()
    {
        return $this->player_effective->toIso8601String();
    }

    /**
     * @return mixed
     */
    public function getExpirationStampAttribute()
    {
        return $this->player_expiration->toIso8601String();
    }

    /**
     * @return mixed
     */
    public function getGroupAttribute()
    {
        $group = MainHelper::specialGroups($this->player_group);

        return $group;
    }
}
