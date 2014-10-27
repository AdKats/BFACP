<?php namespace ADKGamers\Webadmin\Models\Battlefield;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use DB;
use Eloquent;
use Schema;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\AdKats\Setting AS AdKatsSetting;

class Server extends Eloquent
{
    /**
     * Table to use
     *
     * @var string
     */
    protected $table = 'tbl_server';

    /**
     * Table primary key to use
     *
     * @var string
     */
    protected $primaryKey = 'ServerID';

    /**
     * Table columns that cannot be edited by mass assignment
     *
     * @var array
     */
    protected $guarded = array( 'ServerID' );

    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = FALSE;

    /**
     * Removes any unwanted text from the server name
     *
     * For example, the following string
     *
     *      =ADK= EU #3 | Conquest Large Rotation | ADKGamers.com |
     *
     * would be reduced to this
     *
     *      EU Conquest Large Rotation
     *
     * based on this comma delimited list
     *
     *     =ADK=,24/7,#3,ADKGamers.com,|
     *
     * @return string
     */
    public function strip($string = NULL)
    {
        // Check if the table exists
        if(empty($string))
        {
            // Preform the query to get the information
            $strip = DB::table('bfadmincp_settings_gameserver')->where('server_id', $this->ServerID)->pluck('name_strip');

            // Did we get a result?
            if(!empty($strip))
            {
                // Return back the formated name
                return preg_replace('/\s\s+/', ' ', trim(str_replace(explode(',', $strip), '', $this->ServerName)));
            }
            else return NULL;
        }

        return preg_replace('/\s\s+/', ' ', trim(str_replace(explode(',', $string), '', $this->ServerName)));;
    }

    public function chat()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\Battlefield\\Chatlog', 'ServerID', 'ServerID');
    }

    public function gameIdent()
    {
        return Helper::getGameName($this->GameID);
    }

    public function scopeBF3($query, $all = false)
    {
        if($all)
        {
            return $query->where('GameID', BF3_DB_ID);
        }
        else
        {
            return $query->where('GameID', BF3_DB_ID)->where('ConnectionState', 'on');
        }
    }

    public function scopeBF4($query, $all = false)
    {
        if($all)
        {
            return $query->where('GameID', BF4_DB_ID);
        }
        else
        {
            return $query->where('GameID', BF4_DB_ID)->where('ConnectionState', 'on');
        }
    }

    public function adkatsConfig()
    {
        return $this->hasMany('ADKGamers\\Webadmin\\Models\\AdKats\\Setting', 'server_id', 'ServerID');
    }

    public function setting()
    {
        return $this->hasOne('ADKGamers\\Webadmin\\Models\\Battlefield\\Setting', 'server_id', 'ServerID');
    }
}
