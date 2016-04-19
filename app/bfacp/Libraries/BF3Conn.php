<?php namespace BFACP\Libraries;

use BFACP\Battlefield\Server\Server;
use Exception;

/*
 * BF3Conn - PHP class for communicating with a Battlefield 3 gameserver
 * http://sf.net/p/bf3conn/
 * Copyright (c) 2010 by JLNNN <JLN@hush.ai>
 * Copyright (c) 2011 by an3k <an3k@users.sf.net>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 * <b>Project page:</b>
 * http://sf.net/p/bf3conn/<br />
 * <br /><br />
 * <b>Message board:</b><br /><br />
 * http://sf.net/p/bf3conn/discussion/<br />
 * <br /><br />
 * <b>Support:</b><br /><br />
 * http://sf.net/p/bf3conn/support/
 *
 * @author  an3k <an3k@users.sf.net>
 * @version 0.91b
 */
class BF3Conn
{
    private $_serverIP = null;

    private $_serverRconQueryPort = null;

    private $_clientSequenceNr = 0;

    private $_sock = null;

    private $_connection = false;

    private $_playerdata = null; // used for caching

    private $_playerdata_admin = null; // used for caching

    private $_serverdata = null; // used for caching

    private $_isLoggedIn = false;

    private $_sockType = null;

    /*-- configuration vars --*/

    private $_globalMsg = [
        'PLAYER_NOT_FOUND'        => 'PlayerNotFoundError',
        'TEAM_NOT_FOUND'          => 'TeamNameNotFoundError',
        'SQUAD_NOT_FOUND'         => 'SquadNameNotFoundError',
        'PLAYMODE_NOT_FOUND'      => 'PlaymodeNameNotFoundError',
        'MAPNAME_NOT_FOUND'       => 'MapNameNotFoundError',
        'ADMIN_YELL_DURATION_MAX' => 60,
        'NOT_LOGGED_IN'           => 'NotLoggedInAsAdmin',
        'LOGIN_FAILED'            => 'LoginFailed',
    ];

    private $_globalVars = [];

    /*-- constructor and destructor --*/

    /**
     * @param Server $server
     * @param string $debug for debugging, use "-d"
     */
    public function __construct(Server $server, $debug = '-d')
    {
        $this->_globalVars = [
            'mapsFileXML'           => $server->maps_file_path,
            'playmodesFileXML'      => $server->modes_file_path,
            'squadnamesFileXML'     => $server->squads_file_path,
            'teamnamesFileXML'      => $server->teams_file_path,
            'defaultServerResponse' => 'OK',
            'cachingEnabled'        => true // change this to false if you want caching disabled
        ];

        if ($this->_serverIP == null) {
            $this->_serverIP = $server->ip;
            $this->_serverRconQueryPort = $server->port;
            $this->_connection = $this->_openConnection($debug);
        }
    }

    private function _openConnection($debug = null)
    {
        $connection = false;

        if (function_exists('socket_create') && function_exists('socket_connect') && function_exists('socket_strerror') && function_exists('socket_last_error') && function_exists('socket_set_block') && function_exists('socket_read') && function_exists('socket_write') && function_exists('socket_close')) {
            $this->_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            socket_set_option($this->_sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 5, 'usec' => 0]);
            socket_set_option($this->_sock, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 5, 'usec' => 0]);

            @$connection = socket_connect($this->_sock, $this->_serverIP, $this->_serverRconQueryPort);
            if ($debug == '-d') {
                echo '[DEBUG]: ' . socket_strerror(socket_last_error()) . ".\n";
            }
            if ($connection) {
                socket_set_block($this->_sock);
            }

            $this->_sockType = 1;
        } else {
            if (function_exists('fsockopen')) {
                if ($debug == '-d') {
                    @$this->_sock = fsockopen('tcp://' . $this->_serverIP, $this->_serverRconQueryPort, $errno, $errstr,
                        10);
                    if (!$this->_sock) {
                        echo '[DEBUG]: ' . $errno . ' - ' . $errstr . "\n";
                    }
                } else {
                    @$this->_sock = fsockopen('tcp://' . $this->_serverIP, $this->_serverRconQueryPort);
                }

                $connection = $this->_sock;

                $this->_sockType = 2;
            }
        }

        return $connection;
    }

    /*-- required methods for communicating with the gameserver --*/

    public function __destruct()
    {
        if ($this->_connection) {
            $this->_closeConnection();
            $this->_connection = false;
        }
    }

    private function _closeConnection()
    {
//      $this->_clientRequest("quit");

        if ($this->_sockType == 1) {
            socket_close($this->_sock);
        } else {
            fclose($this->_sock);
        }

        $this->_sockType = null;
    }

    /**
     * plain text login to gameserver<br />
     * [ RCON password MUST NOT contain whitespaces!! ]
     *
     * @param String
     *
     * @return String
     */
    public function loginInsecure($rconPassword)
    {
        $loginStatus = $this->_array2String($this->_clientRequest('login.plainText ' . $rconPassword), 0);

        if ($loginStatus == $this->_globalVars['defaultServerResponse']) {
            $this->_isLoggedIn = true;

            return $loginStatus;
        } else {
            return $this->_globalMsg['LOGIN_FAILED'];
        }
    }

    private function _array2String($array, $key = 1)
    {
        return $array[ $key ];
    }

    private function _clientRequest($clientRequest)
    {
        $data = $this->_encodeClientRequest($clientRequest);

        if ($this->_sockType == 1) {
            socket_write($this->_sock, $data, strlen($data));
        } else {
            fwrite($this->_sock, $data, strlen($data));
        }

        $receiveBuffer = '';
        list($packet, $receiveBuffer) = $this->_receivePacket($receiveBuffer);
        list($isFromServer, $isResponse, $sequence, $requestAnswer) = $this->_decodePacket($packet);

        return $requestAnswer;
    }

    private function _encodeClientRequest($data)
    {
        $packet = $this->_encodePacket(false, false, $this->_clientSequenceNr, $data);
        $this->_clientSequenceNr = ($this->_clientSequenceNr + 1) & 0x3fffffff;

        return $packet;
    }

    private function _encodePacket($isFromServer, $isResponse, $sequence, $data)
    {
        $data = explode(' ', $data);
        if ($data[0] == 'admin.yell' && isset($data[1])) {
            $adminYell = [$data[0], '', '', ''];

            $yellStyle = '';
            $yellKey = 0;
            foreach ($data as $key => $content) {
                if ($key != 0) {
                    if ($content == '{%player%}') {
                        $yellStyle = 'player';
                        $yellKey = $key;
                        break;
                    } else {
                        if ($content == '{%team%}') {
                            $yellStyle = 'team';
                            $yellKey = $key;
                            break;
                        } else {
                            if ($content == '{%all%}') {
                                $yellStyle = 'all';
                                $yellKey = $key;
                                break;
                            }
                        }
                    }
                }
            }

            if ($yellStyle == 'all') {
                foreach ($data as $key => $content) {
                    if ($key != 0 && $key < $yellKey - 1) {
                        $adminYell[1] .= $content . ' ';
                    } else {
                        if ($key == $yellKey) {
                            $adminYell[3] = $yellStyle;
                        } else {
                            if ($key == $yellKey - 1) {
                                $adminYell[2] = $data[ $yellKey - 1 ];
                            }
                        }
                    }
                }

                $adminYell[1] = trim($adminYell[1]);
            } else {
                if ($yellStyle == 'player' || $yellStyle == 'team') {
                    $adminYell[4] = '';

                    foreach ($data as $key => $content) {
                        if ($key != 0 && $key < $yellKey - 1) {
                            $adminYell[1] .= $content . ' ';
                        } else {
                            if ($key == $yellKey) {
                                $adminYell[3] = $yellStyle;
                            } else {
                                if ($key == $yellKey - 1) {
                                    $adminYell[2] = $data[ $yellKey - 1 ];
                                } else {
                                    if ($key > $yellKey) {
                                        $adminYell[4] .= $content . ' ';
                                    }
                                }
                            }
                        }
                    }

                    $adminYell[4] = trim($adminYell[4]); // trim whitespaces
                }
            }

            $data = $adminYell;
        } else {
            if ($data[0] == 'vars.serverDescription' && isset($data[1])) {
                $serverDesc = [$data[0], ''];
                foreach ($data as $key => $value) {
                    if ($key != 0) {
                        $serverDesc[1] .= $value . ' ';
                    }
                }
                $serverDesc[1] = trim($serverDesc[1]);

                $data = $serverDesc;
            } else {
                if ($data[0] == 'admin.kickPlayer' && isset($data[1])) {
                    $reason = false;
                    foreach ($data as $key => $value) {
                        if ($value == '{%reason%}') {
                            $reason = true;
                        }
                    }

                    if (!$reason) {
                        $kickPlayer = [$data[0], ''];
                        foreach ($data as $key => $value) {
                            if ($key != 0) {
                                $kickPlayer[1] .= $value . ' ';
                            }
                        }
                        $kickPlayer[1] = trim($kickPlayer[1]);
                    } else {
                        $kickPlayer = [$data[0], '', ''];
                        $i = 0;
                        foreach ($data as $key => $value) {
                            if ($key != 0) {
                                if ($value == '{%reason%}') {
                                    $i = $key;
                                }

                                if ($i == 0) {
                                    $kickPlayer[1] .= $value . ' ';
                                } else {
                                    if ($key != $i) {
                                        $kickPlayer[2] .= $value . ' ';
                                    }
                                }
                            }
                        }
                        $kickPlayer[1] = trim($kickPlayer[1]); // trim whitespaces
                        $kickPlayer[2] = trim($kickPlayer[2]); // trim whitespaces
                    }

                    $data = $kickPlayer;
                } else {
                    if ($data[0] == 'banList.add' || $data[0] == 'banList.remove' && isset($data[1])) {
                        $dataCount = count($data) - 1;
                        $banPlayer = [$data[0], $data[1], ''];
                        foreach ($data as $key => $value) {
                            if ($key != 0 && $key != 1) {
                                if ($data[0] == 'banList.add' && $key != $dataCount) {
                                    $banPlayer[2] .= $value . ' ';
                                } else {
                                    if ($data[0] == 'banList.remove') {
                                        $banPlayer[2] .= $value . ' ';
                                    }
                                }
                            }
                        }

                        $banPlayer[2] = trim($banPlayer[2]); // trim whitespace

                        if ($data[0] == 'banList.add') {
                            $banPlayer[3] = $data[ $dataCount ];
                        }

                        $data = $banPlayer;
                    } else {
                        if ($data[0] == 'admin.listPlayers' || $data[0] == 'listPlayers' && isset($data[1])) {
                            $listPlayer = [$data[0]];
                            if ($data[1] != 'all') {
                                if ($data[1] == 'player') {
                                    $listPlayer[1] = $data[1];
                                    $listPlayer[2] = '';
                                    foreach ($data as $key => $value) {
                                        if ($key != 0 && $key != 1) {
                                            $listPlayer[2] .= $value . ' ';
                                        }
                                    }

                                    $listPlayer[2] = trim($listPlayer[2]); // trim ending whitespace
                                }
                                if ($data[1] == 'team') {
                                    $listPlayer[1] = $data[1];
                                    $listPlayer[2] = '';
                                    foreach ($data as $key => $value) {
                                        if ($key != 0 && $key != 1) {
                                            $listPlayer[2] .= $value . ' ';
                                        }
                                    }

                                    $listPlayer[2] = trim($listPlayer[2]); // trim ending whitespace
                                }
                            } else {
                                $listPlayer[1] = $data[1];
                            }

                            $data = $listPlayer;
                        } else {
                            if ($data[0] == 'reservedSlots.addPlayer' || $data[0] == 'reservedSlots.removePlayer' && isset($data[1])) {
                                $reservedSlots = [$data[0], ''];
                                foreach ($data as $key => $value) {
                                    if ($key != 0) {
                                        $reservedSlots[1] .= $value . ' ';
                                    }
                                }

                                $reservedSlots[1] = trim($reservedSlots[1]); // trim whitespace

                                $data = $reservedSlots;
                            } else {
                                if ($data[0] == 'admin.say' && isset($data[1])) {
                                    $adminSay = [$data[0], '', '', ''];
                                    $i = 0;
                                    foreach ($data as $key => $value) {
                                        if ($key != 0) {
                                            if ($value == '{%player%}' || $value == '{%team%}' || $value == '{%all%}') {
                                                $i = $key;
                                                $adminSay[2] = preg_replace('/[{}%]/', '', $value);
                                            }
                                            if ($i == 0) {
                                                $adminSay[1] .= $value . ' ';
                                            } else {
                                                if ($key != $i && $adminSay[2] != 'all') {
                                                    $adminSay[3] .= $value . ' ';
                                                }
                                            }
                                        }
                                    }

                                    $adminSay[1] = trim($adminSay[1]); // trim whitespace
                                    $adminSay[3] = trim($adminSay[3]); // trim whitespace

                                    if ($adminSay[2] == 'all') {
                                        unset($adminSay[3]);
                                    }

                                    $data = $adminSay;
                                } else {
                                    if ($data[0] == 'admin.killPlayer' && isset($data[1])) {
                                        $adminKillPlayer = [$data[0], ''];
                                        $i = 0;
                                        foreach ($data as $key => $value) {
                                            if ($key != 0) {
                                                $adminKillPlayer[1] .= $value . ' ';
                                            }
                                        }

                                        $adminKillPlayer[1] = trim($adminKillPlayer[1]); // trim whitespace

                                        $data = $adminKillPlayer;
                                    } else {
                                        if ($data[0] == 'admin.movePlayer' && isset($data[1])) {
                                            $dataCount = count($data) - 3;
                                            $adminMovePlayer = [$data[0], '', '', '', ''];
                                            $i = 0;
                                            foreach ($data as $key => $value) {
                                                if ($key != 0 && $key < $dataCount) {
                                                    $adminMovePlayer[1] .= $value . ' ';
                                                }
                                            }

                                            $adminMovePlayer[1] = trim($adminMovePlayer[1]); // trim whitespace
                                            $adminMovePlayer[2] = $data[ $dataCount ];
                                            $adminMovePlayer[3] = $data[ $dataCount + 1 ];
                                            $adminMovePlayer[4] = $data[ $dataCount + 2 ];

                                            $data = $adminMovePlayer;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $encodedHeader = $this->_encodeHeader($isFromServer, $isResponse, $sequence);
        $encodedNumWords = $this->_encodeInt32(count($data));
        list($wordsSize, $encodedWords) = $this->_encodeWords($data);
        $encodedSize = $this->_encodeInt32($wordsSize + 12);

        return $encodedHeader . $encodedSize . $encodedNumWords . $encodedWords;
    }

    private function _encodeHeader($isFromServer, $isResponse, $sequence)
    {
        $header = $sequence & 0x3fffffff;
        if ($isFromServer) {
            $header += 0x80000000;
        }
        if ($isResponse) {
            $header += 0x40000000;
        }

        return pack('I', $header);
    }

    private function _encodeInt32($size)
    {
        return pack('I', $size);
    }

    private function _encodeWords($words)
    {
        $size = 0;
        $encodedWords = '';
        foreach ($words as $word) {
            $strWord = $word;
            $encodedWords .= $this->_encodeInt32(strlen($strWord));
            $encodedWords .= $strWord;
            $encodedWords .= "\x00";
            $size += strlen($strWord) + 5;
        }

        return [
            $size,
            $encodedWords,
        ];
    }

    private function _receivePacket($receiveBuffer)
    {
        $count = 0;
        while (!$this->_containsCompletePacket($receiveBuffer)) {
            if ($this->_sockType == 1) {
                $socketbuffer = @socket_read($this->_sock, 4096);
                if ($socketbuffer == false) {
                    throw new Exception('Could not read packet data from game server.');
                }
                $receiveBuffer .= $socketbuffer;
            } else {
                $socketbuffer = fread($this->_sock, 4096);
                if ($socketbuffer == false) {
                    throw new Exception('Could not read packet data from game server.');
                }
                $receiveBuffer .= $socketbuffer;
            }
        }

        $packetSize = $this->_decodeInt32(substr($receiveBuffer, 4, 4));

        $packet = substr($receiveBuffer, 0, $packetSize);
        $receiveBuffer = substr($receiveBuffer, $packetSize, strlen($receiveBuffer));

        return [
            $packet,
            $receiveBuffer,
        ];
    }

    private function _containsCompletePacket($data)
    {
        if (strlen($data) < 8) {
            return false;
        }

        if (strlen($data) < $this->_decodeInt32(substr($data, 4, 4))) {
            return false;
        }

        return true;
    }

    private function _decodeInt32($data)
    {
        $decode = unpack('I', $data);

        return $decode[1];
    }

    private function _decodePacket($data)
    {
        list($isFromServer, $isResponse, $sequence) = $this->_decodeHeader($data);
        $wordsSize = $this->_decodeInt32(substr($data, 4, 4)) - 12;
        $words = $this->_decodeWords($wordsSize, substr($data, 12));

        return [
            $isFromServer,
            $isResponse,
            $sequence,
            $words,
        ];
    }

    private function _decodeHeader($data)
    {
        $header = unpack('I', $data);

        return [
            $header & 0x80000000,
            $header & 0x40000000,
            $header & 0x3fffffff,
        ];
    }

    /*-- internal methods --*/

    private function _decodeWords($size, $data)
    {
        $numWords = $this->_decodeInt32($data);
        $offset = 0;
        while ($offset < $size) {
            $wordLen = $this->_decodeInt32(substr($data, $offset, 4));
            $word = substr($data, $offset + 4, $wordLen);
            $words[] = $word;
            $offset += $wordLen + 5;
        }

        return $words;
    }

    /**
     * salted hash login to gameserver<br />
     * [ RCON password MUST NOT contain whitespaces!! ]
     *
     * @param String
     *
     * @return String
     */
    public function loginSecure($rconPassword)
    {
        $salt = $this->_array2String($this->_clientRequest('login.hashed'));

        $hashedPW = $this->_hex_str($salt) . $rconPassword;
        $saltedHashedPW = strtoupper(md5($hashedPW));

        $loginStatus = $this->_array2String($this->_clientRequest('login.hashed ' . $saltedHashedPW), 0);

        if ($loginStatus == $this->_globalVars['defaultServerResponse']) {
            $this->_isLoggedIn = true;

            return $loginStatus;
        } else {
            return $this->_globalMsg['LOGIN_FAILED'];
        }
    }

    private function _hex_str($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[ $i ] . $hex[ $i + 1 ]));
        }

        return $string;
    }

    /**
     * logging out
     *
     * @return String
     */
    public function logout()
    {
        $this->_isLoggedIn = false;

        return $this->_array2String($this->_clientRequest('logout'), 0);
    }

    /*-- login and logout --*/

    /**
     * disconnecting from the gameserver
     *
     * @return String
     */
    public function quit()
    {
        return $this->_array2String($this->_clientRequest('quit'), 0);
    }

    /**
     * if logged in with rcon && successful, return true, otherwise false
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    /**
     * returns the name of the given squad<br /><br />
     * example: getSquadName(1) - will return "Bravo Squad"
     *
     * @param Integer
     *
     * @return String
     */
    public function getSquadName($squadID)
    {
        $squadNamesXML = simplexml_load_file($this->_globalVars['squadnamesFileXML']);
        $squadName = $this->_globalMsg['SQUAD_NOT_FOUND'];

        for ($i = 0; $i <= (count($squadNamesXML->squad) - 1); $i++) {
            if ($squadID == $squadNamesXML->squad[ $i ]->attributes()->id) {
                $squadName = $squadNamesXML->squad[ $i ]->attributes()->name;
            }
        }

        return $squadName;
    }

    /**
     * gets the teamname of a given map, playmode and teamid (DIFFERENT IN SQDM!)
     *
     * @param String
     * @param String
     * @param Integer
     * @param Integer (optional) - if playmode = SQDM, a squadid is required!
     *
     * @return String
     */
    public function getTeamName($mapURI, $playmodeURI, $teamID, $squadID = null)
    {
        $teamNameXML = simplexml_load_file($this->_globalVars['teamnamesFileXML']);
        $teamName = $this->_globalMsg['TEAM_NOT_FOUND'];

        $playModes = count($teamNameXML->teamName[0]) - 1;
        if ($playModes >= $teamID) {
            for ($i = 0; $i <= $playModes; $i++) {
                if ($teamNameXML->teamName[0]->playMode[ $i ]->attributes()->uri == $playmodeURI) {
                    $teamName = $teamNameXML->teamName[0]->playMode[ $i ]->team[ $teamID ]->name;
                }
            }
        }

        return $teamName;
    }

    /**
     * returns the server ip as a string
     *
     * @return String
     */
    public function getServerIP()
    {
        return $this->_serverIP;
    }

    /*-- replacements --*/

    /**
     * returns the server name as a string
     *
     * @return String
     */
    public function getServerName()
    {
        $serverInfo = $this->getServerInfo();

        return $this->_array2String($serverInfo);
    }

    /**
     * returns the server information as an array
     *
     * @return array
     */
    public function getServerInfo()
    {
        if ($this->_serverdata == null || !$this->_globalVars['cachingEnabled']) {
            $this->_serverdata = $this->_clientRequest('serverInfo');
        }

        return $this->_serverdata;
    }

    /**
     * returns the max amount of players allowed on server as an integer
     *
     * @return Integer
     */
    public function getMaxPlayers()
    {
        $serverInfo = $this->getServerInfo();

        return (int)$this->_array2String($serverInfo, 3);
    }

    /**
     * returns the current playmode (human readable) as a string
     *
     * @return String
     */
    public function getCurrentPlaymodeName()
    {
        $serverInfo = $this->getServerInfo();

        return $this->getPlaymodeName($this->getCurrentPlaymode());
    }

    /*-- server information --*/

    /**
     * returns the name of the given playmode<br /><br />
     * example: getPlaymodeName("RUSH")
     *
     * @param String
     *
     * @return String name of the given playmode
     */
    public function getPlaymodeName($playmodeURI)
    {
        $playModesXML = simplexml_load_file($this->_globalVars['playmodesFileXML']);
        $playmodeName = $this->_globalMsg['PLAYMODE_NOT_FOUND'];

        for ($i = 0; $i <= (count($playModesXML->playmode) - 1); $i++) {
            if ($playmodeURI == $playModesXML->playmode[ $i ]->attributes()->uri) {
                $playmodeName = $playModesXML->playmode[ $i ]->attributes()->name;
            }
        }

        return $playmodeName;
    }

    /**
     * returns the current playmode as a string
     *
     * @return String
     */
    public function getCurrentPlaymode()
    {
        $serverInfo = $this->getServerInfo();

        return $this->_array2String($serverInfo, 4);
    }

    /**
     * returns the current map (human readable) as a string
     *
     * @return String
     */
    public function getCurrentMapName()
    {
        $serverInfo = $this->getServerInfo();

        return $this->getMapName($this->getCurrentMap());
    }

    /**
     * returns the name of the given map<br /><br />
     * example: getMapName("Levels/MP_002")
     *
     * @param String
     *
     * @return String name of the given map
     */
    public function getMapName($mapURI)
    {
        $mapNamesXML = simplexml_load_file($this->_globalVars['mapsFileXML']);
        $mapName = $this->_globalMsg['MAPNAME_NOT_FOUND'];
        $currentPlaymode = $this->getCurrentPlaymode();

        for ($i = 0; $i <= (count($mapNamesXML->map) - 1); $i++) {
            if (strcasecmp($mapURI,
                    $mapNamesXML->map[ $i ]->attributes()->uri) == 0 && $currentPlaymode == $mapNamesXML->map[ $i ]->attributes()->playmode
            ) {
                $mapName = $mapNamesXML->map[ $i ]->attributes()->name;
            }
        }

        return $mapName;
    }

    /**
     * returns the current map as a string
     *
     * @return String
     */
    public function getCurrentMap()
    {
        $serverInfo = $this->getServerInfo();

        return $this->_array2String($serverInfo, 5);
    }

    /**
     * returns the build-id of the game
     *
     * @return Integer
     */
    public function getVersionID()
    {
        return (int)$this->_array2String($this->getVersion(), 2);
    }

    /**
     * returns the server version as an array
     *
     * @return array
     */
    public function getVersion()
    {
        return $this->_clientRequest('version');
    }

    /**
     * returns the game type currently running
     *
     * @return String
     */
    public function getGameType()
    {
        return $this->_array2String($this->getVersion());
    }

    /**
     * returns the current round of the game
     *
     * @return Integer
     */
    public function getCurrentGameRound()
    {
        return (int)$this->_array2String($this->getServerInfo(), 6) + 1;
    }

    /**
     * returns the max amount of rounds of the current game
     *
     * @return Integer
     */
    public function getGameMaxRounds()
    {
        return (int)$this->_array2String($this->getServerInfo(), 7);
    }

    /**
     * returns the current teamscores
     * FFIXX doesn't work yet
     *
     * @return Integer
     */
    public function getTeamScores()
    {
        return (int)$this->_array2String($this->getServerInfo(), 8);
    }

    /**
     * returns the current online state of the gameserver
     * FFIXX doesn't work with the current bf3 server release
     *
     * @return String
     */
    public function getOnlineState()
    {
        return $this->_array2String($this->getServerInfo(), 11);
    }

    /**
     * returns list of all players on the server with the team specified, but with zeroed out GUIDs
     *
     * @param string $team
     *
     * @return array
     */
    public function getPlayerlistTeam($team = '')
    {
        if (!isset($team) || $team == '') {
            $team = 'all';
        } else {
            $team = 'team ' . $team;
        }

        return $this->_clientRequest('listPlayers ' . $team);
    }

    /**
     * returns list of all playernames on server (useful for other functions)
     *
     * @return array
     */
    public function getPlayerlistNames()
    {
        $players = $this->getPlayerlist();
        $playersAmount = $this->getCurrentPlayers();

        if ($playersAmount == 0) {
            $playersNames = [];
        }

        $playersParameters = (int)$players[1];
        for ($i = 0; $i < $playersAmount; $i++) {
            $playersNames[] = $players[ ($playersParameters) * $i + $playersParameters + 3 ];
        }

        return $playersNames;
    }

    /**
     * returns list of all players on the server, but with zeroed out GUIDs
     *
     * @return array
     */
    public function getPlayerlist()
    {
        if ($this->_playerdata == null || !$this->_globalVars['cachingEnabled']) {
            $this->_playerdata = $this->_clientRequest('listPlayers all');
        }

        return $this->_playerdata;
    }

    /**
     * returns the current players on server as an integer
     *
     * @return Integer
     */
    public function getCurrentPlayers()
    {
        $serverInfo = $this->getServerInfo();

        return (int)$this->_array2String($serverInfo, 2);
    }

    /**
     * returns true if server is available, otherwise false
     *
     * @see isConnected()
     * @return boolean
     */
    public function isServerOnline()
    {
        return $this->isConnected();
    }

    /**
     * returns true if connected to a gameserver, otherwise false
     *
     * @see isServerOnline()
     * @return boolean
     */
    public function isConnected()
    {
        return $this->_connection;
    }

    /**
     * returns the gamepassword as a string
     *
     * @return String
     */
    public function adminVarGetGamepassword()
    {
        return $this->_array2String($this->_clientRequest('vars.gamePassword'));
    }

    /**
     * gets the full gamedata of all players on the gameserver
     *
     * @return array
     */
    public function adminGetPlayerlist()
    {
        if ($this->_playerdata_admin == null || !$this->_globalVars['cachingEnabled']) {
            $this->_playerdata_admin = $this->_clientRequest('admin.listPlayers all');
        }

        return $this->_playerdata_admin;
    }

    /**
     * returns all commands available on the server - requires login
     *
     * @return array
     */
    public function adminGetAllCommands()
    {
        return $this->_clientRequest('serverInfo');
    }

    /*-- admin server information --*/

    /**
     * returns true/false, if server events are enabled in this connection or
     * not
     *
     * @return array
     */
    public function adminEventsEnabledStatusGet()
    {
        return $this->_clientRequest('eventsEnabled');
    }

    /**
     * returns the clantag of a given playername
     *
     * @param String
     *
     * @return String
     */
    public function getPlayerClantag($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[12])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return $this->_array2String($playerInfo, 12);
    }

    /**
     * TODO: check for caching if playername = all
     * returns gamedata of given playername with zeroed out GUID
     *
     * @param String (if not set, all players will be listed)
     *
     * @return array
     */
    public function getPlayerdata($playerName = '')
    {
        if (!isset($playerName) || $playerName == '') {
            $playerName = 'all';
        } else {
            $playerName = 'player ' . $playerName;
        }

        return $this->_clientRequest('listPlayers ' . $playerName);
    }

    /**
     * returns the playername of a given playername
     *
     * @param String
     *
     * @return String
     */
    public function getPlayername($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[11])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return $this->_array2String($playerInfo, 11);
    }

    /**
     * returns the current kills of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerKills($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[15])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($playerInfo, 15);
    }

    /**
     * sets the server events on/off in this connection<br />
     * [ Useless, if you set them on and use this class only ;) ]
     *
     * @param boolean
     *
     * @return array
     */
    //  function adminEventsEnabledStatusSet($boolean) {
    //      return $this->_clientRequest("eventsEnabled " .
    //      $this->_bool2String($boolean));
    //  }

    /**
     * returns the current deaths of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerDeaths($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[16])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($playerInfo, 16);
    }

    /**
     * returns the score of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerScore($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[17])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($playerInfo, 17);
    }

    /**
     * returns the ping of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerPing($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[11])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($this->_clientRequest('player.ping ' . $playerName), 1);
    }

    /**
     * sends an admin-yell message to a specified player (or all)<br />
     * example: adminYellMessage("Storm the front!", "JLNNN") - send the message to player "JLNNN"
     * TODO: Need fix for sending messages to squads
     * TODO: Cut strings with length more than 100 chars
     *
     * @param String
     * @param String  (optional) - if not set, message will be sent to all
     *                players
     * @param Integer (optional) - amount of time the message will be displayed,
     *                must be 1-60
     *
     * @return String
     */
    public function adminYellMessage($text, $playerName = '{%all%}', $durationInMS = 10)
    {
        if ($durationInMS > $this->_globalMsg['ADMIN_YELL_DURATION_MAX']) {
            $durationInMS = $this->_globalMsg['ADMIN_YELL_DURATION_MAX'];
        }

        if ($playerName != '{%all%}') {
            $playerName = '{%player%}' . ' ' . $playerName;
        }

        return $this->_array2String($this->_clientRequest('admin.yell ' . $text . ' ' . $durationInMS . ' ' . $playerName),
            0);
    }

    /**
     * sends an admin-yell message to a specified team<br />
     * example: adminYellMessageToTeam("Storm the front!", 1) - send the message to teamID 1
     * TODO: Cut strings with length more than 100 chars
     *
     * @param String
     * @param Integer
     * @param Integer (optional) - amount of time the message will be displayed,
     *                must be 1-60
     *
     * @return String
     */
    public function adminYellMessageToTeam($text, $teamID, $durationInMS = 10)
    {
        if ($durationInMS > $this->_globalMsg['ADMIN_YELL_DURATION_MAX']) {
            $durationInMS = $this->_globalMsg['ADMIN_YELL_DURATION_MAX'];
        }

        return $this->_array2String($this->_clientRequest('admin.yell ' . $text . ' ' . $durationInMS . ' {%team%} ' . $teamID),
            0);
    }

    /**
     * sends a chat message to a player. the message must be less than 100 characters long.
     *
     * @param String
     * @param String
     *
     * @return String
     */
    public function adminSayMessageToPlayer($playerName, $text)
    {
        return $this->_array2String($this->_clientRequest('admin.say ' . $text . ' {%player%} ' . $playerName), 0);
    }

    /**
     * sends a chat mesage to a team. the message must be less than 100 characters long.
     *
     * @param String
     * @param Integer
     *
     * @return String
     */
    public function adminSayMessageToTeam($teamID, $text)
    {
        return $this->_array2String($this->_clientRequest('admin.say ' . $text . ' {%team%} ' . $teamID), 0);
    }

    /**
     * sends a chat mesage to all. the message must be less than 100 characters long.
     *
     * @param String
     *
     * @return String
     */
    public function adminSayMessageToAll($text)
    {
        return $this->_array2String($this->_clientRequest('admin.say ' . $text . ' {%all%}'), 0);
    }

    /*-- admin commands --*/

    /**
     * runs the next level on maplist
     *
     * @return String
     */
    public function adminRunNextLevel()
    {
        return $this->_array2String($this->_clientRequest('admin.runNextLevel'), 0);
    }

    /**
     * restarts the current level
     *
     * @return String
     */
    public function adminRestartMap()
    {
        return $this->_array2String($this->_clientRequest('mapList.restartRound'), 0);
    }

    /**
     * sets a new playlist<br /><br />
     * example: adminSetPlaylist("SQDM") - for setting playmode to 'Squad Deathmatch'
     *
     * @param String
     *
     * @return String
     */
    public function adminSetPlaylist($playmodeURI)
    {
        return $this->_array2String($this->_clientRequest('admin.setPlaylist ' . $playmodeURI), 0);
    }

    /**
     * returns all available playmodes on server
     *
     * @return array
     */
    public function adminGetPlaylists()
    {
        return $this->_clientRequest('admin.getPlaylists');
    }

    /**
     * returns current playmode on server
     *
     * @see getCurrentPlaymode()
     * @return String
     */
    public function adminGetPlaylist()
    {
        return $this->_array2String($this->_clientRequest('admin.getPlaylist'));
    }

    /**
     * loads the maplist
     *
     * @see adminMaplistList()
     * @return array
     */
    public function adminMaplistLoad()
    {
        return $this->_clientRequest('mapList.load');
    }

    /**
     * TODO: planned feature adminSetNextLevel() ?
     * sets the next level to play
     *
     * @param String
     *
     * @return String
     */
    //function adminSetNextLevel($mapURI) {
    //
    //}

    /**
     * saves the current maplist to file
     *
     * @return String
     */
    public function adminMaplistSave()
    {
        return $this->_array2String($this->_clientRequest('mapList.save'), 0);
    }

    /**
     * returns the maplist from map file
     *
     * @return array
     */
    public function adminMaplistList()
    {
        return $this->_clientRequest('mapList.list');
    }

    /**
     * clears the maplist file
     *
     * @return String
     */
    public function adminMaplistClear()
    {
        return $this->_array2String($this->_clientRequest('mapList.clear'), 0);
    }

    /**
     * removes a given map from maplist<br />
     * [ index = Integer! ]
     *
     * @param Integer
     *
     * @return String
     */
    public function adminMaplistRemove($rowID)
    {
        return $this->_array2String($this->_clientRequest('mapList.remove ' . $rowID), 0);
    }

    /**
     * appends a given map to the end of the maplist file
     *
     * @param String
     *
     * @return String
     */
    public function adminMaplistAppend($mapURI)
    {
        return $this->_array2String($this->_clientRequest('mapList.append ' . $mapURI), 0);
    }

    /**
     * gets index of next map to be run
     *
     * @return Integer
     */
    public function adminMaplistGetNextMapIndex()
    {
        return (int)$this->_array2String($this->_clientRequest('mapList.getMapIndices'), 2);
    }

    /**
     * sets index of next map to be run<br />
     * [ index = Integer! ]
     *
     * @param Integer
     *
     * @return String
     */
    public function adminMaplistSetNextMapIndex($index)
    {
        return $this->_array2String($this->_clientRequest('mapList.nextLevelIndex ' . $index), 0);
    }

    /**
     * adds map with name at the specified index to the maplist
     *
     * @param Integer
     * @param String
     *
     * @return String
     */
    public function adminMaplistInsertMapInIndex($index, $mapURI)
    {
        return $this->_array2String($this->_clientRequest('mapList.insert ' . $index . ' ' . $mapURI), 0);
    }

    /**
     * gets list of maps supported by given playmode
     *
     * @param String
     *
     * @return array
     */
    public function adminGetSupportedMaps($playmode)
    {
        return $this->_clientRequest('admin.supportedMaps ' . $playmode);
    }

    /**
     * kicks a specified player by playername
     *
     * @param String
     *
     * @return String
     */
    public function adminKickPlayer($playerName)
    {
        return $this->_array2String($this->_clientRequest('admin.kickPlayer ' . $playerName), 0);
    }

    /**
     * kicks a specified player by playername with a given kickreason
     *
     * @param String
     * @param String (optional) - if not set, default kickreason is given
     *
     * @return String
     */
    public function adminKickPlayerWithReason($playerName, $reason = 'Kicked by administrator')
    {
        return $this->_array2String($this->_clientRequest('admin.kickPlayer ' . $playerName . ' {%reason%} ' . $reason),
            0);
    }

    /**
     * bans a specified player by playername for a given range of time.<br />
     * range of time can be: perm = permanent, round = current round<br />
     * if no range is given, the player will be banned permanently
     * TODO: ban for xx seconds
     * TODO: banreason
     *
     * @param String
     * @param String (optional) - if not set, given player will be banned permanently
     *
     * @return String
     */
    public function adminBanAddPlayername($playerName, $timerange = 'perm')
    {
        return $this->_array2String($this->_clientRequest('banList.add name ' . $playerName . ' ' . $timerange));
    }

    /**
     * bans a specified player by given playerip<br />
     * range of time can be: perm = permanent, round = current round<br />
     * if no range is given, the ip will be banned permanently
     * TODO: ban for xx seconds
     * TODO: banreason
     *
     * @param String
     * @param String (optional) - if not set, ip will be banned permanently
     *
     * @return String
     */
    public function adminBanAddPlayerIP($playerIP, $timerange = 'perm')
    {
        return $this->_array2String($this->_clientRequest('banList.add ip ' . $playerIP . ' ' . $timerange), 0);
    }

    /**
     * bans a specified player by given playerguid<br />
     * range of time can be: perm = permanent, round = current round<br />
     * if no range is given, the ip will be banned permanently
     * TODO: ban for xx seconds
     * TODO: banreason
     *
     * @param     String
     * @param int $timerange
     *
     * @return String
     */
    public function adminBanAddPlayerGUID($playerName, $timerange = 2)
    {
        $playerGUID = $this->adminGetPlayerGUID($playerName);
        if ($playerGUID != $this->_globalMsg['PLAYER_NOT_FOUND']) {
            return $this->_array2String($this->_clientRequest('banList.add GUID ' . $playerGUID . ' ' . $timerange), 0);
        } else {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }
    }

    /**
     * returns the GUID of a given playername
     *
     * @param String
     *
     * @return String
     */
    public function adminGetPlayerGUID($playerName)
    {
        $playerInfo = $this->adminGetPlayerdata($playerName);
        if (!isset($playerInfo[12])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return $this->_array2String($playerInfo, 12);
    }

    /**
     * gets the gamedata of a given playername on the gameserver
     * TODO: check for playerNotFound
     *
     * @param String (optional) - if not set, all players will be listed
     *
     * @return array
     */
    public function adminGetPlayerdata($playerName = '')
    {
        if (!isset($playerName) || $playerName == '') {
            $playerName = 'all';
        } else {
            $playerName = 'player ' . $playerName;
        }

        return $this->_clientRequest('admin.listPlayers ' . $playerName);
    }

    /**
     * saves the current banlist to banlist file
     *
     * @return String
     */
    public function adminBanlistSave()
    {
        return $this->_array2String($this->_clientRequest('banList.save'), 0);
    }

    /**
     * loads the banlist from banlist file
     *
     * @return String
     */
    public function adminBanlistLoad()
    {
        return $this->_array2String($this->_clientRequest('banList.load'), 0);
    }

    /**
     * unbans a player by playername
     *
     * @param String
     *
     * @return String
     */
    public function adminBanRemovePlayername($playerName)
    {
        return $this->_array2String($this->_clientRequest('banList.remove name ' . $playerName), 0);
    }

    /**
     * unbans a player by playerip
     *
     * @param String
     *
     * @return String
     */
    public function adminBanRemovePlayerIP($playerIP)
    {
        return $this->_array2String($this->_clientRequest('banList.remove ip ' . $playerIP), 0);
    }

    /**
     * unbans a player by playerip
     *
     * @param String
     *
     * @return String
     */
    public function adminBanRemovePlayerGUID($playerGUID)
    {
        return $this->_array2String($this->_clientRequest('banList.remove GUID ' . $playerGUID), 0);
    }

    /**
     * clears all bans from playername banlist
     *
     * @return String
     */
    public function adminBanlistClear()
    {
        return $this->_array2String($this->_clientRequest('banList.clear'), 0);
    }

    /**
     * lists all bans from banlist
     *
     * @param string $offset
     *
     * @return array
     */
    public function adminBanlistList($offset = '0')
    {
        return $this->_clientRequest('banList.list ' . $offset);
    }

    /**
     * loads the file containing all reserved slots<br />
     * [ I don't know if this function is useful.. ]
     *
     * @return String
     */
    public function adminReservedSlotsLoad()
    {
        return $this->_array2String($this->_clientRequest('reservedSlotsList.load'), 0);
    }

    /**
     * saves made changes to reserved slots file
     *
     * @return String
     */
    public function adminReservedSlotsSave()
    {
        return $this->_array2String($this->_clientRequest('reservedSlotsList.save'), 0);
    }

    /**
     * adds a player by given playername to reserved slots file
     *
     * @param String
     *
     * @return String
     */
    public function adminReservedSlotsAddPlayer($playerName)
    {
        return $this->_array2String($this->_clientRequest('reservedSlotsList.addPlayer ' . $playerName), 0);
    }

    /**
     * removes a player by given playername from reserved slots file
     *
     * @param String
     *
     * @return String
     */
    public function adminReservedSlotsRemovePlayer($playerName)
    {
        return $this->_array2String($this->_clientRequest('reservedSlotsList.removePlayer ' . $playerName), 0);
    }

    /**
     * clears the file containing all reserved slots
     *
     * @return String
     */
    public function adminReservedSlotsClear()
    {
        return $this->_array2String($this->_clientRequest('reservedSlotsList.clear'), 0);
    }

    /**
     * lists all playernames in reserved slots file
     *
     * @return array
     */
    public function adminReservedSlotsList()
    {
        return $this->_clientRequest('reservedSlotsList.list');
    }

    /**
     * kills the given player without counting this death on the playerstats
     *
     * @param $playerName
     *
     * @return String
     */
    public function adminKillPlayer($playerName)
    {
        return $this->_array2String($this->_clientRequest('admin.killPlayer ' . $playerName), 0);
    }

    /**
     * moves the given player to the opponent team<br />
     * if $forceKill is true, the player will be killed<br />
     * [ Works only if the player is dead! Otherwise $forceKill has to be true! ]
     *
     * @param $playerName
     * @param $forceKill (optional) - if not set, player will not be killed
     *
     * @return String
     */
    public function adminMovePlayerSwitchTeam($playerName, $forceKill = false)
    {
        $playerTeam = $this->getPlayerTeamID($playerName);

        $forceKill = $this->_bool2String($forceKill);

        switch ($playerTeam) {
            case '1':
                $newPlayerTeam = 2;
                break;
            case 2:
                $newPlayerTeam = '1';
                break;
        }

        return $this->_array2String($this->_clientRequest('admin.movePlayer ' . $playerName . ' ' . $newPlayerTeam . ' 0 ' . $forceKill),
            0);
    }

    /**
     * returns the teamid of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerTeamID($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[13])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($playerInfo, 13);
    }

    private function _bool2String($boolean)
    {
        $onOrOff = '';
        if ($boolean) {
            $onOrOff = 'true';
        } else {
            $onOrOff = 'false';
        }

        return $onOrOff;
    }

    /**
     * Gives player leader of their current squad
     *
     * @param  string $playerName
     *
     * @return string
     */
    public function adminSquadLeader($playerName)
    {
        $teamID = $this->getPlayerTeamID($playerName);
        $squadID = $this->getPlayerSquadID($playerName);

        return $this->_array2String($this->_clientRequest('squad.leader ' . $teamID . ' ' . $squadID . ' ' . $playerName),
            0);
    }

    /**
     * returns the squadid of a given playername
     *
     * @param String
     *
     * @return Integer
     */
    public function getPlayerSquadID($playerName)
    {
        $playerInfo = $this->getPlayerdata($playerName);
        if (!isset($playerInfo[14])) {
            return $this->_globalMsg['PLAYER_NOT_FOUND'];
        }

        return (int)$this->_array2String($playerInfo, 14);
    }

    /**
     * Checks if the squad is locked
     *
     * @param  integer $teamID
     * @param  integer $squadID
     *
     * @return boolean
     */
    public function adminGetSquadPrivate($teamID, $squadID)
    {
        return $this->_array2boolean($this->_clientRequest('squad.private ' . $teamID . ' ' . $squadID));
    }

    private function _array2boolean($array, $key = 1)
    {
        if (isset($array[ $key ]) && $array[ $key ] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set the squad to private or not
     *
     * @param  integer $teamID
     * @param  integer $squadID
     * @param  boolean $private
     *
     * @return boolean
     */
    public function adminSetSquadPrivate($teamID, $squadID, $private = true)
    {
        return $this->_array2boolean($this->_clientRequest('squad.private ' . $teamID . ' ' . $squadID . ' ' . $this->_bool2String($private)));
    }

    /**
     * Get all squads that have players in them on a specific team
     *
     * @param  integer $teamID
     *
     * @return array
     */
    public function adminSquadListActive($teamID)
    {
        return $this->_clientRequest('squad.listActive ' . $teamID);
    }

    /**
     * Get player count and names of soldiers in a specific squad
     *
     * @param  integer $teamID
     * @param  integer $squadID
     *
     * @return array
     */
    public function adminSquadListPlayer($teamID, $squadID)
    {
        return $this->_clientRequest('squad.listPlayers ' . $teamID . ' ' . $squadID);
    }

    /**
     * moves the given player to another specific squad<br />
     * if $forceKill is true, the player will be killed<br />
     * [ Works only if the player is dead! Otherwise $forceKill has to be true! ]
     *
     * @param $playerName
     * @param $newSquadID
     * @param $forceKill (optional) - if not set, player will not be killed
     * @param $newTeamID (optional) - if not set, player will stay in his team
     *
     * @return String
     */
    public function adminMovePlayerSwitchSquad($playerName, $newSquadID, $forceKill = false, $newTeamID = null)
    {
        if ($newTeamID == null || !is_int($newTeamID)) {
            $newTeamID = $this->getPlayerTeamID($playerName);
        }

        $forceKill = $this->_bool2String($forceKill);

        return $this->_array2String($this->_clientRequest('admin.movePlayer ' . $playerName . ' ' . $newTeamID . ' ' . $newSquadID . ' ' . $forceKill),
            0);
    }

    /**
     * ends the current round, declaring the given teamId as winner
     *
     * @param $teamId
     *
     * @return String
     */
    public function adminEndRound($teamId)
    {
        return $this->_array2String($this->_clientRequest('mapList.endRound ' . $teamId), 0);
    }

    /*-- admin server settings --*/

    /**
     * sets 3d spotting on maps on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSet3dSpotting($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.3dSpotting ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if 3d spotting is enabled or not
     *
     * @return boolean
     */
    public function adminVarGet3dSpotting()
    {
        return $this->_array2boolean($this->_clientRequest('vars.3dSpotting'));
    }

    /**
     * sets the 3rd person vehicle cam on/off<br /><br />
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSet3rdPersonVehiCam($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.3pCam ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if 3rd person vehicle cam is enabled or not
     *
     * @return boolean
     */
    public function adminVarGet3rdPersonVehiCam()
    {
        return $this->_array2boolean($this->_clientRequest('vars.3pCam'));
    }

    /**
     * sets if all unlocks are available on/off<br /><br />
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetAllUnlocksUnlocked($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.allUnlocksUnlocked ' . $this->_bool2String($boolean)),
            0);
    }

    /**
     * gets true/false, if all unlocks are available or not
     *
     * @return boolean
     */
    public function adminVarGetAllUnlocksUnlocked()
    {
        return $this->_array2boolean($this->_clientRequest('vars.allUnlocksUnlocked'));
    }

    /**
     * sets teambalance on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetTeambalance($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.autoBalance ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if teambalance is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetTeambalance()
    {
        return $this->_array2boolean($this->_clientRequest('vars.autoBalance'));
    }

    /**
     * sets the banner url to given address
     * [ The banner url needs to be less than 64 characters long.<br />
     * The banner needs to be a 512x64 picture and smaller than 127kbytes. ]
     *
     * @param String
     *
     * @return String
     */
    public function adminVarSetBannerURL($string)
    {
        return $this->_array2String($this->_clientRequest('vars.bannerUrl ' . $string), 0);
    }

    /**
     * gets the banner url
     *
     * @return String
     */
    public function adminVarGetBannerURL()
    {
        return $this->_array2String($this->_clientRequest('vars.bannerUrl'));
    }

    /**
     * sets the bullet damage modifier in %
     *
     * @param $integer
     *
     * @return String
     */
    public function adminVarSetbulletDamageModifier($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.bulletDamage ' . $integer), 0);
    }

    /**
     * gets the bullet damage modifier
     *
     * @return Integer
     */
    public function adminVarGetbulletDamageModifier()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.bulletDamage'));
    }

    /**
     * sets crosshair on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetCrosshair($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.crossHair ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if crosshair is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetCrosshair()
    {
        return $this->_array2boolean($this->_clientRequest('vars.crossHair'));
    }

    /**
     * sets friendly fire on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetFriendlyFire($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.friendlyFire ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if friendly fire is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetFriendlyFire()
    {
        return $this->_array2boolean($this->_clientRequest('vars.friendlyFire'));
    }

    /**
     * sets a new password on the gameserver.<br />
     * password MUST NOT contain whitespaces!!<br /><br />
     * to clear the password, use adminVarGetGamepassword("")
     *
     * @param String
     *
     * @return String
     */
    public function adminVarSetGamepassword($serverPassword)
    {
        return $this->_array2String($this->_clientRequest('vars.gamePassword ' . $serverPassword), 0);
    }

    /**
     * sets hud on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetHud($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.hud ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if hud is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetHud()
    {
        return $this->_array2boolean($this->_clientRequest('vars.hud'));
    }

    /**
     * sets how many seconds a player can be idle before he/she is kicked from the server
     *
     * @param $idleTimeoutInteger
     *
     * @return String
     */
    public function adminVarSetIdleTimeout($idleTimeoutInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.idleTimeout ' . $idleTimeoutInteger), 0);
    }

    /**
     * gets the current idle time allowed
     *
     * @return Integer
     */
    public function adminVarGetIdleTimeout()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.idleTimeout'));
    }

    /**
     * sets the number of rounds a player who is kicked for idle is banned
     * set to 0 to disable
     *
     * @param $idleBanRoundsInteger
     *
     * @return String
     */
    public function adminVarSetidleBanRounds($idleBanRoundsInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.idleBanRounds ' . $idleBanRoundsInteger), 0);
    }

    /**
     * gets the number of rounds a player who is kicked for idle is banned
     *
     * @return Integer
     */
    public function adminVarGetidleBanRounds()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.idleBanRounds'));
    }

    /**
     * sets killcam on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetKillCam($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.killCam ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if killcam is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetKillCam()
    {
        return $this->_array2boolean($this->_clientRequest('vars.killCam'));
    }

    /**
     * sets minimap on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetMiniMap($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.miniMap ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if minimap is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetMiniMap()
    {
        return $this->_array2boolean($this->_clientRequest('vars.miniMap'));
    }

    /**
     * sets minimap spotting on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetMiniMapSpotting($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.miniMapSpotting ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if minimap spotting is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetMiniMapSpotting()
    {
        return $this->_array2boolean($this->_clientRequest('vars.miniMapSpotting'));
    }

    /**
     * sets nametag on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetNameTag($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.nameTag ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if nametag is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetNameTag()
    {
        return $this->_array2boolean($this->_clientRequest('vars.nameTag'));
    }

    /**
     * sets onlySquadLeaderSpawn on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetOnlySquadLeaderSpawn($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.onlySquadLeaderSpawn ' . $this->_bool2String($boolean)),
            0);
    }

    /**
     * gets true/false, if onlySquadLeaderSpawn is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetOnlySquadLeaderSpawn()
    {
        return $this->_array2boolean($this->_clientRequest('vars.onlySquadLeaderSpawn'));
    }

    /**
     * sets the man down time modifier in %
     *
     * @param $integer
     *
     * @return String
     */
    public function adminVarSetPlayerManDownTimeModifier($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.playerManDownTime ' . $integer), 0);
    }

    /**
     * gets the man down time modifier
     *
     * @return Integer
     */
    public function adminVarGetPlayerManDownTimeModifier()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.playerManDownTime'));
    }

    /**
     * sets the respawn time modifier in %
     *
     * @param $integer
     *
     * @return String
     */
    public function adminVarSetPlayerRespawnTimeModifier($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.playerRespawnTime ' . $integer), 0);
    }

    /**
     * gets the respawn time modifier
     *
     * @return Integer
     */
    public function adminVarGetPlayerRespawnTimeModifier()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.playerRespawnTime'));
    }

    /**
     * gets true/false, if punkbuster is enabled or not
     * FFIXX Only works for Rush right now
     *
     * @return boolean
     */
    public function adminVarGetPunkbuster()
    {
        $serverInfo = $this->getServerInfo();

        return $this->_array2boolean($serverInfo, 14);
    }

    /**
     * gets true/false if ranked server settings are enabled or not
     * FFIXX Only works for Rush right now
     *
     * @return boolean
     */
    public function adminVarGetRanked()
    {
        $serverInfo = $this->getServerInfo();

        return $this->_array2boolean($serverInfo, 13);
    }

    /**
     * sets health regeneration on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetRegenerateHealth($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.regenerateHealth ' . $this->_bool2String($boolean)), 0);
    }

    /**
     * gets true/false, if health regeneration is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetRegenerateHealth()
    {
        return $this->_array2boolean($this->_clientRequest('vars.regenerateHealth'));
    }

    /**
     * sets how many players are required to begin a round
     *
     * @param String
     *
     * @return String
     */
    public function adminVarSetRoundStartPlayerCount($string)
    {
        return $this->_array2String($this->_clientRequest('vars.roundStartPlayerCount ' . $string), 0);
    }

    /**
     * gets how many players are required to begin a round
     *
     * @return String
     */
    public function adminVarGetRoundStartPlayerCount()
    {
        return $this->_array2String($this->_clientRequest('vars.roundStartPlayerCount'));
    }

    /**
     * sets at how many players the server switches to pre-round
     *
     * @param String
     *
     * @return String
     */
    public function adminVarSetRoundRestartPlayerCount($string)
    {
        return $this->_array2String($this->_clientRequest('vars.roundRestartPlayerCount ' . $string), 0);
    }

    /**
     * gets at how many players the server switches to pre-round
     *
     * @return String
     */
    public function adminVarGetRoundRestartPlayerCount()
    {
        return $this->_array2String($this->_clientRequest('vars.roundRestartPlayerCount'));
    }

    /**
     * sets the server description to given text<br />
     * [ Character '|' doesn't work for now as a 'newline'. ]<br /><br />
     * ##Request from RSPs: In addition being able to enter a new line would be<br />
     * great, BF2142 used the "|" character as newline.
     *
     * @param String
     *
     * @return String
     */
    public function adminVarSetServerDescription($string)
    {
        return $this->_array2String($this->_clientRequest('vars.serverDescription ' . $string), 0);
    }

    /**
     * gets the server description
     *
     * @return String
     */
    public function adminVarGetServerDescription()
    {
        return $this->_array2String($this->_clientRequest('vars.serverDescription'));
    }

    /**
     * sets a new servername
     *
     * @param $serverName
     *
     * @return String
     */
    public function adminVarSetServername($serverName)
    {
        return $this->_array2String($this->_clientRequest('vars.serverName ' . $serverName), 0);
    }

    /**
     * sets the soldier health modifier in %
     *
     * @param $integer
     *
     * @return String
     * @internal param $soldierHealthInteger
     */
    public function adminVarSetSoldierHealthModifier($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.soldierHealth ' . $soldierHealthInteger), 0);
    }

    /**
     * gets the soldier health modifier
     *
     * @return Integer
     */
    public function adminVarGetSoldierHealthModifier()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.soldierHealth'));
    }

    /**
     * sets the number of teamkills allowed during one round, before the game kicks
     * the player in question
     *
     * @param $teamKillCountForKickInteger
     *
     * @return String
     */
    public function adminVarSetTeamKillCountForKick($teamKillCountForKickInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.teamKillCountForKick ' . $teamKillCountForKickInteger),
            0);
    }

    /**
     * gets the number of teamkills allowed during one round
     *
     * @return Integer
     */
    public function adminVarGetTeamKillCountForKick()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.teamKillCountForKick'));
    }

    /**
     * sets the highest kill-value allowed before a player is kicked for teamkilling<br />
     * set to 0 to disable kill value mechanism
     *
     * @param $teamKillValueForKickInteger
     *
     * @return String
     */
    public function adminVarSetTeamKillValueForKick($teamKillValueForKickInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.teamKillValueForKick ' . $teamKillValueForKickInteger),
            0);
    }

    /**
     * gets the highest kill-value allowed before a player is kicked for teamkilling
     *
     * @return Integer
     */
    public function adminVarGetTeamKillValueForKick()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.teamKillValueForKick'));
    }

    /**
     * sets the value of a teamkill (adds to the player's current kill-value)
     *
     * @param $teamKillValueIncreaseInteger
     *
     * @return String
     */
    public function adminVarSetTeamKillValueIncrease($teamKillValueIncreaseInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.teamKillValueIncrease ' . $teamKillValueIncreaseInteger),
            0);
    }

    /**
     * gets the value of a teamkill
     *
     * @return Integer
     */
    public function adminVarGetTeamKillValueIncrease()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.teamKillValueIncrease'));
    }

    /**
     * sets how much every player's kill-value should decrease per second
     *
     * @param $teamKillValueDecreaseInteger
     *
     * @return String
     */
    public function adminVarSetTeamKillValueDecreasePerSecond($teamKillValueDecreaseInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.teamKillValueDecreasePerSecond ' . $teamKillValueDecreaseInteger),
            0);
    }

    /**
     * gets the decrease value
     *
     * @return Integer
     */
    public function adminVarGetTeamKillValueDecreasePerSecond()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.teamKillValueDecreasePerSecond'));
    }

    /**
     * sets the number of teamkill-kicks allowed before the game bans the player in question
     *
     * @param $teamKillKickForBanInteger
     *
     * @return String
     */
    public function adminVarSetTeamKillKickForBan($teamKillKickForBanInteger)
    {
        return $this->_array2String($this->_clientRequest('vars.teamKillKickForBan ' . $teamKillKickForBanInteger), 0);
    }

    /**
     * gets the number of teamkill-kicks allowed
     *
     * @return Integer
     */
    public function adminVarGetTeamKillKickForBan()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.teamKillKickForBan'));
    }

    /**
     * sets vehicle spawn on/off
     *
     * @param boolean
     *
     * @return String
     */
    public function adminVarSetVehicleSpawnAllowed($boolean)
    {
        return $this->_array2String($this->_clientRequest('vars.vehicleSpawnAllowed ' . $this->_bool2String($boolean)),
            0);
    }

    /**
     * gets true/false, if vehicle spawn is enabled or not
     *
     * @return boolean
     */
    public function adminVarGetVehicleSpawnAllowed()
    {
        return $this->_array2boolean($this->_clientRequest('vars.vehicleSpawnAllowed'));
    }

    /**
     * sets the vehicle spawn modifier in %
     *
     * @param $integer
     *
     * @return String
     * @internal param $vehicleSpawnDelayInteger
     */
    public function adminVarSetVehicleSpawnDelayModifier($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.vehicleSpawnDelay ' . $vehicleSpawnDelayInteger), 0);
    }

    /**
     * gets the soldier health modifier
     *
     * @return Integer
     */
    public function adminVarGetVehicleSpawnDelayModifier()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.vehicleSpawnDelay'));
    }

    public function adminVarSetEffectivePlayers($integer)
    {
        return $this->_array2String($this->_clientRequest('vars.maxPlayers ' . $integer), 0);
    }

    /**
     * gets scale factor for number of tickets to end round, in percent
     *
     * @return integer
     */
    public function adminVarGetGameModeCounter()
    {
        return (int)$this->_array2String($this->_clientRequest('vars.gameModeCounter'), 1);
    }

    /**
     * Tabulates a result containing columns and rows
     *
     * @param  array $res
     *
     * @return array
     */
    public function tabulate($res)
    {
        array_shift($res);
        $nColumns = $res[0];
        $columns = [];

        for ($i = 1; $i <= $nColumns; $i++) {
            $columns[] = $res[ $i ];
        }

        $nRows = $res[9];
        $rows = [
            'players' => [],
            'columns' => [],
        ];

        for ($n = 0; $n < $nRows; $n++) {
            $row = [];

            for ($j = 0; $j < count($columns); $j++) {
                $value = $res[ ++$i ];
                $row[ $columns[ $j ] ] = is_numeric($value) ? (int)$value : $value;
            }

            $rows['players'][] = $row;
        }

        $rows['columns'] = $columns;

        return $rows;
    }

    // TODO: server moderation mode
}
