<?php

namespace BFACP\Adkats\Account;

use BFACP\Elegant;

/**
 * Class Soldier.
 */
class Soldier extends Elegant
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
    protected $table = 'adkats_usersoldiers';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Fields allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['user_id'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo(\BFACP\Adkats\Account\User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo(\BFACP\Battlefield\Player::class, 'player_id');
    }
}
