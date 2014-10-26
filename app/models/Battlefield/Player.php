<?php namespace ADKGamers\Webadmin\Models\Battlefield;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use Eloquent;
use Illuminate\Support\Facades\DB;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Player;

class Player extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'tbl_playerdata';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'PlayerID';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'PlayerID' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    protected $appends = array( 'CountryName' );

    /**
     * Returns all records done by the player
     *
     * @return array
     */
    public function recordsBy()
    {
        return $this->hasMany( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'source_id', 'PlayerID' );
    }

    /**
     * Returns all records against the player
     *
     * @return array
     */
    public function recordsOn()
    {
        return $this->hasMany( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'target_id', 'PlayerID' );
    }

    public function recentBan()
    {
        return $this->hasOne( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Ban', 'player_id', 'PlayerID' );
    }

    public function recentBanExist()
    {
        $banExist = Ban::where('player_id', $this->PlayerID)->first();

        if($banExist)
        {
            return TRUE;
        }
        else return FALSE;
    }

    public function previousBans()
    {
        return $this->hasMany( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'target_id', 'PlayerID' )
                ->join('adkats_bans', function($join)
                {
                    $join->on('adkats_records_main.record_id', '!=', 'adkats_bans.latest_record_id')
                         ->on('adkats_bans.player_id', '=', 'adkats_records_main.target_id');
                })
                ->whereIn('command_action', [7, 8, 72, 73])->get();
    }

    public function previousSoldiers()
    {
        return $this->hasMany( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'target_id', 'PlayerID' )
                ->where('command_type', 48)
                ->orderBy('record_id', 'desc')->get();
    }

    public function previousIps()
    {
        return $this->hasMany( 'ADKGamers\\Webadmin\\Models\\Battlefield\\Record', 'target_id', 'PlayerID' )
                ->where('command_type', 49)
                ->where('record_message', '!=', 'No previous IP on record')
                ->orderBy('record_id', 'desc')->get();
    }

    /**
     * Returns the players reputation
     *
     * @return array
     */
    public function reputation()
    {
        return $this->belongsTo('ADKGamers\\Webadmin\\Models\\Battlefield\\Reputation', 'PlayerID', 'player_id');
    }

    public function gameIdent()
    {
        return Helper::getGameName($this->GameID);
    }

    public function chat()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\Battlefield\\Chatlog', 'logPlayerID', 'PlayerID');
    }

    public function lastSeen()
    {
        return DB::table('tbl_server_player')->join('tbl_playerstats', 'tbl_playerstats.StatsID', '=', 'tbl_server_player.StatsID')
                ->where('tbl_server_player.PlayerID', $this->PlayerID)->orderBy('tbl_playerstats.LastSeenOnServer', 'desc')->first();
    }

    public function points()
    {
        return DB::table('adkats_infractions_global')->where('player_id', $this->PlayerID)->first();
    }

    public function pointsPerServer()
    {
        return DB::table('adkats_infractions_server')->where('player_id', $this->PlayerID)->get();
    }

    public function getCountryNameAttribute()
    {
        if(is_null($this->CountryCode)) return NULL;
        return Helper::countries($this->CountryCode);
    }
}
