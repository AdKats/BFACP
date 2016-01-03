<?php namespace BFACP\Helpers;

use Exception;

class Battlefield extends Main
{
    /**
     * Gets the name of the squad by ID
     *
     * @param  integer $id Squad ID
     *
     * @return string Squad Name
     * @throws Exception
     */
    public function squad($id)
    {
        if (!is_numeric($id)) {
            throw new Exception('Invalid squad id: ' . $id);
        }

        switch (intval($id)) {
            case 0:
                $sq = 'None';
                break;
            case 1:
                $sq = 'Alpha';
                break;
            case 2:
                $sq = 'Bravo';
                break;
            case 3:
                $sq = 'Charlie';
                break;
            case 4:
                $sq = 'Delta';
                break;
            case 5:
                $sq = 'Echo';
                break;
            case 6:
                $sq = 'Foxtrot';
                break;
            case 7:
                $sq = 'Golf';
                break;
            case 8:
                $sq = 'Hotel';
                break;
            case 9:
                $sq = 'India';
                break;
            case 10:
                $sq = 'Juliet';
                break;
            case 11:
                $sq = 'Kilo';
                break;
            case 12:
                $sq = 'Lima';
                break;
            case 13:
                $sq = 'Mike';
                break;
            case 14:
                $sq = 'November';
                break;
            case 15:
                $sq = 'Oscar';
                break;
            case 16:
                $sq = 'Papa';
                break;
            case 17:
                $sq = 'Quebec';
                break;
            case 18:
                $sq = 'Romeo';
                break;
            case 19:
                $sq = 'Sierra';
                break;
            case 20:
                $sq = 'Tango';
                break;
            case 21:
                $sq = 'Uniform';
                break;
            case 22:
                $sq = 'Victor';
                break;
            case 23:
                $sq = 'Whiskey';
                break;
            case 24:
                $sq = 'Xray';
                break;
            case 25:
                $sq = 'Yankee';
                break;
            case 26:
                $sq = 'Zulu';
                break;
            case 27:
                $sq = 'Haggard';
                break;
            case 28:
                $sq = 'Sweetwater';
                break;
            case 29:
                $sq = 'Preston';
                break;
            case 30:
                $sq = 'Redford';
                break;
            case 31:
                $sq = 'Faith';
                break;
            case 32:
                $sq = 'Celeste';
                break;
            default:
                $sq = null;
                break;
        }

        // Returns the squad name
        return $sq;
    }

    /**
     * Calculates kill/death ratio
     *
     * @param  integer $kills
     * @param  integer $deaths
     * @param  integer $precision
     *
     * @return float
     */
    public function kd($kills = 0, $deaths = 0, $precision = 2)
    {
        try {
            return round(($kills / $deaths), $precision);
        } catch (Exception $e) {
            if ($kills === 0 && $deaths > 0) {
                return -$deaths;
            }

            return $kills;
        }
    }

    /**
     * Calculates headshot kill ratio
     *
     * @param  integer $headshots
     * @param  integer $kills
     * @param  integer $precision
     *
     * @return float
     */
    public function hsk($headshots = 0, $kills = 0, $precision = 2)
    {
        try {
            return round(($headshots / $kills), $precision);
        } catch (Exception $e) {
            return $headshots;
        }
    }

    /**
     * Calculates the number of tickets on round start
     *
     * @param  string  $gamemode
     * @param  integer $modifier
     * @param  string  $gameName
     *
     * @return integer
     */
    public function startingTickets($gamemode, $modifier, $gameName)
    {
        switch ($gameName) {
            case 'BF4':
                switch ($gamemode) {
                    case 'Chainlink0':
                        $defaultTickets = 1000;
                        break;

                    case 'ConquestLarge0':
                        $defaultTickets = 800;
                        break;

                    case 'ConquestSmall0':
                        $defaultTickets = 400;
                        break;

                    case 'TeamDeathMatch1':
                    case 'TeamDeathMatch0':
                    case 'Elimination0':
                    case 'Obliteration':
                    case 'CarrierAssaultLarge0':
                    case 'CarrierAssaultSmall0':
                        $defaultTickets = 100;
                        break;

                    case 'RushLarge0':
                        $defaultTickets = 100;
                        break;

                    case 'SquadDeathMatch1':
                    case 'SquadDeathMatch0':
                        $defaultTickets = 50;
                        break;

                    case 'Domination0':
                    case 'AirSuperiority0':
                        $defaultTickets = 300;
                        break;

                    case 'CaptureTheFlag0':
                        $defaultTickets = 3;
                        break;

                    case 'GunMaster0':
                    case 'GunMaster1':
                        $defaultTickets = 100;
                        break;

                    default:
                        return null;
                }
                break;

            case 'BF3':
                switch ($gamemode) {
                    case 'ConquestLarge0':
                        $defaultTickets = 800;
                        break;

                    case 'ConquestSmall0':
                        $defaultTickets = 400;
                        break;

                    case 'TeamDeathMatch0':
                        $defaultTickets = 100;
                        break;

                    case 'Domination0':
                    case 'AirSuperiority0':
                        $defaultTickets = 300;
                        break;

                    case 'RushLarge0':
                        $defaultTickets = 75;
                        break;

                    case 'SquadDeathMatch0':
                        $defaultTickets = 50;
                        break;

                    case 'CaptureTheFlag0':
                        $defaultTickets = 3;
                        break;

                    default:
                        return null;
                }
                break;

            case 'BFH':
            case 'BFHL':
                switch ($gamemode) {
                    case 'TurfWarLarge0':
                    case 'TurfWarSmall0':
                    case 'Heist0':
                    case 'Hotwire0':
                    case 'BloodMoney0':
                    case 'Hit0':
                    case 'Hostage0':
                    case 'TeamDeathMatch0':
                    case 'CashGrab0':
                    case 'SquadHeist0':
                        $defaultTickets = 100;
                        break;
                }
                break;

            default:
                return null;
        }

        $startingTicketCount = ($this->divide($defaultTickets, 100) * $modifier);

        return intval($startingTicketCount);
    }

    /**
     * Calculates the round timer on round start
     *
     * @param  string  $gamemode
     * @param  integer $modifier
     * @param  string  $gameName
     *
     * @return integer
     */
    public function roundStartingTimer($gamemode, $modifier, $gameName)
    {
        switch ($gameName) {
            case 'BF4':
                switch ($gamemode) {
                    case 'Chainlink0':
                        $defaultTime = 1200;
                        break;

                    case 'ConquestLarge0':
                    case 'ConquestSmall0':
                    case 'TeamDeathMatch0':
                    case 'TeamDeathMatch1':
                    case 'SquadDeathMatch0':
                    case 'SquadDeathMatch1':
                    case 'Domination0':
                    case 'AirSuperiority0':
                    case 'GunMaster0':
                    case 'GunMaster1':
                        $defaultTime = 3600;
                        break;

                    case 'RushLarge0':
                        $defaultTime = 900 * 3;
                        break;

                    case 'Elimination0':
                        $defaultTime = 600;
                        break;

                    case 'Obliteration':
                    case 'CarrierAssaultLarge0':
                    case 'CarrierAssaultSmall0':
                        $defaultTime = 1800;
                        break;

                    case 'CaptureTheFlag0':
                        $defaultTime = 1200;
                        break;

                    default:
                        return null;
                }
                break;

            case 'BFH':
            case 'BFHL':
                switch ($gamemode) {
                    case 'TurfWarLarge0':
                    case 'TurfWarSmall0':
                    case 'Heist0':
                    case 'Hotwire0':
                    case 'Hit0':
                    case 'Hostage0':
                    case 'TeamDeathMatch0':
                    case 'CashGrab0':
                    case 'SquadHeist0':
                        $defaultTime = 3600;
                        break;

                    case 'BloodMoney0':
                        $defaultTime = 1200;
                        break;
                }
                break;

            default:
                return null;
        }

        $startingRoundTimer = ($this->divide($defaultTime, 100) * $modifier);

        return intval($startingRoundTimer);
    }

    public function mapName($mapURI, $xmlFilePath, $playmodeURI)
    {
        $mapNamesXML = simplexml_load_file($xmlFilePath);
        $mapName = 'MapNameNotFoundError';

        for ($i = 0; $i <= (count($mapNamesXML->map) - 1); $i++) {
            if (strcasecmp($mapURI,
                    $mapNamesXML->map[ $i ]->attributes()->uri) == 0 && $playmodeURI == $mapNamesXML->map[ $i ]->attributes()->playmode
            ) {
                $mapName = $mapNamesXML->map[ $i ]->attributes()->name;
            }
        }

        return !is_string($mapName) ? head($mapName) : $mapName;
    }

    public function playmodeName($playmodeURI, $xmlFilePath)
    {
        $playModesXML = simplexml_load_file($xmlFilePath);
        $playmodeName = 'PlaymodeNameNotFoundError';

        for ($i = 0; $i <= (count($playModesXML->playmode) - 1); $i++) {
            if ($playmodeURI == $playModesXML->playmode[ $i ]->attributes()->uri) {
                $playmodeName = $playModesXML->playmode[ $i ]->attributes()->name;
            }
        }

        return !is_string($playmodeName) ? head($playmodeName) : $playmodeName;
    }
}
