<?php namespace ADKGamers\Webadmin\Libs\Helpers;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Reputation;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Libs\gameMEAPI;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WebadminException, Exception, gameMEAPI_Exception;

class Battlefield
{
    /**
     * Gets the squads name by id
     * @param  integer $squad_id
     * @return string
     */
    static public function squad($squad_id)
    {
        switch((integer) $squad_id)
        {
            case 0:  $sq = 'None'; break;
            case 1:  $sq = 'Alpha'; break;
            case 2:  $sq = 'Bravo'; break;
            case 3:  $sq = 'Charlie'; break;
            case 4:  $sq = 'Delta'; break;
            case 5:  $sq = 'Echo'; break;
            case 6:  $sq = 'Foxtrot'; break;
            case 7:  $sq = 'Golf'; break;
            case 8:  $sq = 'Hotel'; break;
            case 9:  $sq = 'India'; break;
            case 10: $sq = 'Juliet'; break;
            case 11: $sq = 'Kilo'; break;
            case 12: $sq = 'Lima'; break;
            case 13: $sq = 'Mike'; break;
            case 14: $sq = 'November'; break;
            case 15: $sq = 'Oscar'; break;
            case 16: $sq = 'Papa'; break;
            case 17: $sq = 'Quebec'; break;
            case 18: $sq = 'Romeo'; break;
            case 19: $sq = 'Sierra'; break;
            case 20: $sq = 'Tango'; break;
            case 21: $sq = 'Uniform'; break;
            case 22: $sq = 'Victor'; break;
            case 23: $sq = 'Whiskey'; break;
            case 24: $sq = 'Xray'; break;
            case 25: $sq = 'Yankee'; break;
            case 26: $sq = 'Zulu'; break;
            case 27: $sq = 'Haggard'; break;
            case 28: $sq = 'Sweetwater'; break;
            case 29: $sq = 'Preston'; break;
            case 30: $sq = 'Redford'; break;
            case 31: $sq = 'Faith'; break;
            case 32: $sq = 'Celeste'; break;
            default: $sq = NULL; break;
        }

        // Returns the squad name
        return $sq;
    }

    static public function infractionPointsGlobal($playerid)
    {
        return DB::table('adkats_infractions_global')
                ->where('player_id', $playerid)
                ->select('punish_points', 'forgive_points', 'total_points')->first();
    }

    static public function calculKDRatio($kills, $deaths)
    {
        try
        {
            return round($kills / $deaths, 2);
        }
        catch(Exception $e)
        {
            return NULL;
        }
    }

    static public function loadGameMEProfile($game = '', $uniqueid = '', $params = array())
    {
        if(empty($uniqueid))
            return FALSE;
        try
        {
            if(!Config::has('webadmin.GAMEMEAPIURL') || Config::get('webadmin.GAMEMEAPIURL') == '')
                throw new WebadminException('gameME config key is missing');

            $gameME = new gameMEAPI(Config::get('webadmin.GAMEMEAPIURL'));

            // Attempt to load the player
            // Might throw an exception if player hasn't been active
            $playerinfo = $gameME->client_api_playerinfo($game, $uniqueid, GAMEME_DATA_DEFAULT, GAMEME_HASH_UNIQUEID);

            return $playerinfo['playerinfo'][$uniqueid];
        }
        catch(gameMEAPI_Exception $e)
        {
            return $e->getMessage();
        }
        catch(WebadminException $e)
        {
            return $e->getMessage();
        }
    }

    static public function getMapName($mapURI, $xmlFilePath)
    {
        $mapNamesXML = simplexml_load_file($xmlFilePath);
        $mapName = "MapNameNotFoundError";

        for($i = 0; $i <= (count($mapNamesXML->map) - 1); $i++) {
            if(strcasecmp($mapURI, $mapNamesXML->map[$i]->attributes()->uri) == 0) {
                $mapName = $mapNamesXML->map[$i]->attributes()->name;
            }
        }

        return $mapName;
    }

    static public function getPlaymodeName($playmodeURI, $xmlFilePath)
    {
        $playModesXML = simplexml_load_file($xmlFilePath);
        $playmodeName = "PlaymodeNameNotFoundError";

        for($i = 0; $i <= (count($playModesXML->playmode) - 1); $i++) {
            if($playmodeURI == $playModesXML->playmode[$i]->attributes()->uri) {
                $playmodeName = $playModesXML->playmode[$i]->attributes()->name;
            }
        }

        return $playmodeName;
    }

    static public function getStartTicketCount($gamemode, $modifier, $game)
    {
        switch($game)
        {
            case "BF4":
                switch($gamemode)
                {
                    case "Chainlink0":
                        $defaultTickets = 1000;
                    break;

                    case "ConquestLarge0":
                        $defaultTickets = 800;
                    break;

                    case "ConquestSmall0":
                        $defaultTickets = 400;
                    break;

                    case "TeamDeathMatch0":
                        $defaultTickets = 100;
                    break;

                    case "Domination0":
                        $defaultTickets = 300;
                    break;

                    case "Elimination0":
                    case "Obliteration":
                        $defaultTickets = 100;
                    break;

                    case "RushLarge0":
                        $defaultTickets = 75;
                    break;

                    case "SquadDeathMatch0":
                        $defaultTickets = 50;
                    break;

                    case "AirSuperiority0":
                        $defaultTickets = 300;
                    break;

                    case "CaptureTheFlag0":
                        $defaultTickets = 3;
                    break;

                    case "CarrierAssaultLarge0":
                    case "CarrierAssaultSmall0":
                        $defaultTickets = 100;
                    break;

                    default:
                        return NULL;
                }
            break;

            case "BF3":
                switch($gamemode)
                {
                    case "ConquestLarge0":
                        $defaultTickets = 800;
                    break;

                    case "ConquestSmall0":
                        $defaultTickets = 400;
                    break;

                    case "TeamDeathMatch0":
                        $defaultTickets = 100;
                    break;

                    case "Domination0":
                        $defaultTickets = 300;
                    break;

                    case "RushLarge0":
                        $defaultTickets = 75;
                    break;

                    case "SquadDeathMatch0":
                        $defaultTickets = 50;
                    break;

                    case "AirSuperiority0":
                        $defaultTickets = 300;
                    break;

                    case "CaptureTheFlag0":
                        $defaultTickets = 3;
                    break;

                    default:
                        return NULL;
                }
            break;

            default:
                return NULL;
        }

        $startingTicketCount = ( ( $defaultTickets / 100 ) * $modifier);

        return intval($startingTicketCount);
    }

    static public function getStartRoundTimer($gamemode, $modifier, $game)
    {
        switch($game)
        {
            case "BF4":
                switch($gamemode)
                {
                    case "Chainlink0":
                        $defaultTime = 1200;
                    break;

                    case "ConquestLarge0":
                    case "ConquestSmall0":
                    case "TeamDeathMatch0":
                    case "SquadDeathMatch0":
                    case "Domination0":
                    case "AirSuperiority0":
                    case "RushLarge0":
                        $defaultTime = 3600;
                    break;

                    case "Elimination0":
                        $defaultTime = 600;
                    break;

                    case "Obliteration":
                        $defaultTime = 1800;
                    break;

                    case "CaptureTheFlag0":
                        $defaultTime = 1200;
                    break;

                    case "CarrierAssaultLarge0":
                    case "CarrierAssaultSmall0":
                        $defaultTime = 1800;
                    break;

                    default:
                        return NULL;
                }
            break;

            default:
                return NULL;
        }

        $startingRoundTimer = ( ( $defaultTime / 100 ) * $modifier);

        return intval($startingRoundTimer);
    }
}
