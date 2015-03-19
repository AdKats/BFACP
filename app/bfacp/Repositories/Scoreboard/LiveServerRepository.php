<?php namespace BFACP\Repositories\Scoreboard;

use BFACP\Battlefield\Player;
use BFACP\Battlefield\Server;
use BFACP\Contracts\Scoreboard;
use BFACP\Exceptions\PlayerNotFoundException;
use BFACP\Exceptions\RconException;
use BFACP\Repositories\BaseRepository;
use BattlefieldHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use MainHelper;

class LiveServerRepository extends BaseRepository
{
    /**
     * Game DB ID
     *
     * @var integer
     */
    protected $gameID = 0;

    /**
     * Game abbreviation
     *
     * @var null
     */
    protected $gameName = null;

    /**
     * Server DB ID
     *
     * @var integer
     */
    protected $serverID = 0;

    /**
     * Server IPv4 Address
     *
     * @var string
     */
    protected $serverIP = '';

    /**
     * RCON Port
     *
     * @var integer
     */
    protected $port = 0;

    /**
     * Formated Response
     *
     * @var array
     */
    protected $data = [];

    /**
     * BFConn class
     *
     * @var object
     */
    protected $client;

    /**
     * Tells us if we've successfully connected to the server.
     *
     * @var boolean
     */
    protected $connected = false;

    /**
     * Tell us if we are logged in.
     *
     * @var boolean
     */
    protected $authenticated = false;

    /**
     * Server Object
     *
     * @var object
     */
    public $server;

    /**
     * Holds the server infomation block
     * @var array
     */
    protected $serverinfo = [];

    /**
     * Neutral Team
     * @var string
     */
    private $TEAM0 = 'Neutral';

    /**
     * Team 1 Name
     * @var string
     */
    private $TEAM1 = 'US Army';

    /**
     * Team 2 Name
     * @var string
     */
    private $TEAM2 = 'RU Army';

    /**
     * Team 3 Name
     * @var string
     */
    private $TEAM3 = 'US Army';

    /**
     * Team 4 Name
     * @var string
     */
    private $TEAM4 = 'RU Army';

    public function __construct(Server $server)
    {
        parent::__construct();

        $this->server   = $server;
        $this->gameID   = $server->game->GameID;
        $this->gameName = $server->game->Name;
        $this->serverID = $server->ServerID;
        $this->serverIP = $server->ip;
        $this->port     = $server->port;
    }

    /**
     * Attempt to establish connection and login to the gameserver.
     *
     * @return this
     */
    public function attempt()
    {
        if (!is_null($this->client)) {
            return $this;
        }

        switch ($this->gameName) {
            case 'BF3':
                $this->client = App::make('BFACP\Libraries\BF3Conn', [
                    $this->server,
                    false
                ]);
                break;

            case 'BF4':
                $this->client = App::make('BFACP\Libraries\BF4Conn', [
                    $this->server,
                    false
                ]);
                break;

            case 'BFHL':
                $this->client = App::make('BFACP\Libraries\BFHConn', [
                    $this->server,
                    false
                ]);
                break;

            default:
                throw new RconException(500, sprintf('Unsupported game %s', $this->gameName));
        }

        // Update connection state
        $this->connected = $this->client->isConnected();

        // If we are not connected throw exception and abort
        if (!$this->connected) {
            throw new RconException(410, 'Could not connect to server. It may be offline. Please try again later.');
        }

        if (is_null($this->server->setting)) {
            throw new RconException(500, 'Server is not configured yet. Please contact the site administrator.');
        }

        // Attempt to login with provided RCON password
        $this->client->loginSecure($this->server->setting->getPassword());

        // Update authentication state
        $this->authenticated = $this->client->isLoggedIn();

        // If we are connected but not logged in throw exception and abort
        if (!$this->authenticated) {
            throw new RconException(401, 'Incorrect RCON Password. Please contact the site administrator.');
        }

        $this->serverinfo();

        return $this;
    }

    /**
     * Determine if we are connected and authenticated with the gameserver.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->connected && $this->authenticated) {
            return true;
        }

        return false;
    }

    /*======================================
    =            Admin Commands            =
    ======================================*/


    /*==========  Say/Yell  ==========*/

    /**
     * Sends a yell to the entire server, team, or player.
     *
     * @param  string  $message   Message to be sent
     * @param  string  $player    Player Name
     * @param  integer $teamId    Id of the team
     * @param  integer $duration  How long it should stay up in seconds
     * @param  string  $type      All, Team, Player
     * @return boolean            Returns true if it was successful
     */
    public function adminYell($message = '', $player = null, $teamId = null, $duration = 5, $type = 'All')
    {
        // Checks if the message is blank
        if(empty($message)) {
            throw new RconException(400, 'No message provided.');
        }

        if($this->gameName == 'BFHL') {
            $message = str_limit($message, 100);
        }

        switch($type) {
            case "All":
                $response = $this->client->adminYellMessage($message, '{%all%}', $duration);
            break;

            case "Team":
                // Checks if a team id was sent
                if(empty($teamId)) {
                    throw new RconException(400, 'No team id specified.');
                }

                // Checks if the team id is a number or not in valid id list
                if(!is_numeric($teamId) || !in_array($teamId, [1, 2, 3, 4])) {
                    throw new RconException(400, sprintf('"%s" is not a valid team id', $teamId));
                }

                $response = $this->client->adminYellMessageToTeam($message, $teamId, $duration);
            break;

            case "Player":
                // Remove extra whitespace
                $player = trim($player);

                // Checks if name provided is blank
                if(empty($player)) {
                    throw new RconException(400, 'No player name specified.');
                }

                if($this->isValidName($player)) {
                    $response = $this->client->adminYellMessage($message, $player, $duration);

                    // Check if the server returned a player not found error
                    if($response == 'PlayerNotFound') {
                        throw new PlayerNotFoundException(200, sprintf('No player found with the name "%s"', $player));
                    }
                } else {
                    throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
                }
            break;

            default:
                throw new RconException(400, sprintf('"%s" is not a valid type.', $type));
        }

        return true;
    }

    /**
     * Sends a say to the entire server, team, or player.
     *
     * @param  string  $message   Message to be sent
     * @param  string  $player    Player Name
     * @param  integer $teamId    Id of the team
     * @param  string  $type      All, Team, Player
     * @return boolean            Returns true if it was successful
     */
    public function adminSay($message = '', $player = null, $teamId = null, $type = 'All')
    {
        // Checks if the message is blank
        if(empty($message)) {
            throw new RconException(400, 'No message provided.');
        }

        switch($type) {
            case "All":
                $response = $this->client->adminSayMessageToAll($message);
            break;

            case "Team":
                // Checks if a team id was sent
                if(empty($teamId)) {
                    throw new RconException(400, 'No team id specified.');
                }

                // Checks if the team id is a number or not in valid id list
                if(!is_numeric($teamId) || !in_array($teamId, [1, 2, 3, 4])) {
                    throw new RconException(400, sprintf('"%s" is not a valid team id', $teamId));
                }

                $response = $this->client->adminSayMessageToTeam($teamId, $message);
            break;

            case "Player":
                // Remove extra whitespace
                $player = trim($player);

                // Checks if name provided is blank
                if(empty($player)) {
                    throw new RconException(400, 'No player name specified.');
                }

                if($this->isValidName($player)) {
                    $response = $this->client->adminSayMessageToPlayer($player, $message);

                    // Check if the server returned a player not found error
                    if($response == 'PlayerNotFound') {
                        throw new PlayerNotFoundException(200, sprintf('No player found with the name "%s"', $player));
                    }
                } else {
                    throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
                }
            break;

            default:
                throw new RconException(400, sprintf('"%s" is not a valid type.', $type));
        }

        return true;
    }

    /**
     * Sends both a yell and say message to the player
     *
     * @param  string  $player       Name of player
     * @param  string  $message      Message to be sent
     * @param  integer $yellDuration Seconds for yell to stay up
     * @return void
     */
    public function adminTell($player, $message, $yellDuration = 10)
    {
        $this->adminSay($message, $player, null, 'Player');
        $this->adminYell($message, $player, null, $yellDuration, 'Player');

        return;
    }

    /*==========  Player Interaction  ==========*/

    /**
     * Kills the players
     *
     * @param  string $player  Name of player
     * @param  string $message Message to be sent
     * @return boolean
     */
    public function adminKill($player, $message = null)
    {
        if(!empty($message)) {
            $message = sprintf('You were killed by an admin. Reason: %s', $message);
        } else {
            $message = 'You were killed by an admin.';
        }

        if($this->isValidName($player)) {
            $response = $this->client->adminKillPlayer($player);

            // Check if the server returned a player not found error
            if($response == 'InvalidPlayerName') {
                throw new PlayerNotFoundException(200, sprintf('No player found with the name "%s"', $player));
            }

            if($response == 'SoldierNotAlive') {
                throw new PlayerNotFoundException(200, 'Player was already dead.');
            }

            $this->adminTell($player, $message);
        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return true;
    }

    /**
     * Moves the player to a different team and/or squad
     * @param  string  $player  Name of player
     * @param  integer $teamId  Id of team to move to
     * @param  integer $squadId Id of squad to move to
     * @return boolean
     */
    public function adminMovePlayer($player, $teamId = null, $squadId = 0)
    {
        if(!is_numeric($squadId) || empty($squadId) || !in_array($squadId, range(0, 32))) {
            $squadId = 0;
        }

        if(!is_numeric($teamId) || empty($teamId) || !in_array($teamId, range(1, 4))) {
            $teamId = $this->client->getPlayerTeamId($player);
        }

        $teamName  = $this->getTeamName($teamId);
        $squadName = BattlefieldHelper::squad($squadId);

        if(is_array($teamName)) {
            $teamName = $teamName['full_name'];
        }

        if($this->isValidName($player)) {
            if(method_exists($this->client, 'adminGetSquadPrivate') && method_exists($this->client, 'adminSetSquadPrivate')) {
                // Check if squad is private
                if($squadId != 0 && $this->client->adminGetSquadPrivate($teamId, $squadId)) {
                    // Check if squad is full
                    if($playersInSquad = $this->client->adminSquadListPlayer($teamId, $squadId)[1]) {
                        // If squad is full throw an exception with an error message
                        // else unlock the squad so we can move them in.
                        if($playersInSquad == 5) {
                            throw new RconException(200, sprintf('%s squad is full. Cannot switch %s to squad.', $squadName, $player));
                        } else {
                            $this->client->adminSetSquadPrivate($teamId, $squadId, false);
                        }
                    }
                }
            }

            $response = $this->client->adminMovePlayerSwitchSquad($player, (int) $squadId, true, (int) $teamId);

            // Check if the server returned a player not found error
            if($response == 'InvalidPlayerName') {
                throw new PlayerNotFoundException(200, sprintf('No player found with the name "%s"', $player));
            }

            if($response == 'SetSquadFailed') {
                $squadId = 0;
            }

            if($squadId == 0) {
                $message = sprintf('You were switched to team %s and not placed in a squad.', $teamName);
            } else {
                $message = sprintf('You were switched to team %s and placed in squad %s.', $teamName, $squadName);
            }

            $this->adminTell($player, $message, 5);

        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return true;
    }

    /**
     * Kick the player from the server
     *
     * @param  string  $player  Name of player
     * @param  string  $message Message to be sent
     * @param  boolean $isBan   If true then don't send the kick message to server
     * @return boolean
     */
    public function adminKick($player, $message = null, $isBan = false)
    {
        if(empty($message)) {
            $message = 'Kicked by administrator';
        }

        if($this->isValidName($player)) {
            $response = $this->client->adminKickPlayerWithReason($player, $message);

            // Check if the server returned a player not found error
            if($response == 'PlayerNotFound') {
                throw new PlayerNotFoundException(200, sprintf('No player found with the name "%s"', $player));
            }

            // If adminKick was called from the adminBan function do not send the kick message
            if(!$isBan) {
                // Send a general message to the server about the kicked player
                $this->adminSay(sprintf('%s was kicked from the server. Reason: %s', $player, $message));
            }

        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return true;
    }

    /*-----  End of Admin Commands  ------*/



    /*===============================================
    =            General Server Commands            =
    ===============================================*/


    /**
     * Loops over the players and sorts them into teams
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function get($verbose = false)
    {
        if($verbose) {
            $this->verbose();
        }

        return $this->data;
    }

    /**
     * Gathers the server information and puts them in the server array
     * @return this
     */
    private function getMapList()
    {
        $maps = $this->client->adminMaplistList();

        $list = [];

        for ($i = 0; $i < $maps[1]; $i++) {
            $map    = $maps[($maps[2]) * $i + $maps[2]];
            $mode   = $maps[($maps[2]) * $i + $maps[2] + 1];
            $rounds = $maps[($maps[2]) * $i + $maps[2] + 2];

            $list[] = [
                'map'    => [
                    'name' => head($this->client->getMapName($map)),
                    'uri'  => $map
                ],
                'mode'   => [
                    'name' => head($this->client->getPlaymodeName($mode)),
                    'uri'  => $mode
                ],
                'rounds' => (int) $rounds,
                'index'  => $i
            ];
        }

        return $list;
    }

    /**
     * Gets the next map in the rotation
     * @return array
     */
    private function getNextMap()
    {
        $index = $this->client->adminMaplistGetNextMapIndex();
        $maps  = $this->getMapList();

        foreach ($maps as $map) {
            if ($map['index'] == $index) {
                return $map;
            }

        }
    }

    /**
     * Generates the servers maplist into a useable array
     * @return array
     */
    private function getOnlineAdmins()
    {
        $adminlist = DB::select(
            File::get(storage_path() . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'adminList.sql'),
            [$this->gameID]
        );

        foreach (['players', 'spectators', 'commander'] as $type) {
            foreach ($this->data['teams'] as $teamID => $team) {
                if (array_key_exists($type, $team)) {
                    foreach ($team[$type] as $index => $player) {
                        foreach ($adminlist as $index2 => $player2) {
                            $guid = !is_string($player) ? $player['guid'] : $player;

                            if ($guid == $player2->EAGUID) {
                                if ($type == 'commander') {
                                    return false;
                                }

                                $this->data['admins'][$player['name']] = $this->data['teams'][$teamID][$type][$index];
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Simply returns whats in $this->data array
     * @return array
     */
    private function getPlayerDBData()
    {
        // If the server is empty do not continue
        if ($this->data['server']['players']['online'] == 0) {
            return false;
        }

        $players = [];

        foreach ($this->data['teams'] as $teamID => $team) {
            if (array_key_exists('players', $team)) {
                foreach ($team['players'] as $player) {
                    $players[] = $player['guid'];
                }
            }

            if (array_key_exists('spectators', $team)) {
                foreach ($team['spectators'] as $player) {
                    $players[] = $player['guid'];
                }
            }
        }

        // If players array is empty do not continue
        if (empty($players)) {
            return false;
        }

        $playersDB = Player::where('GameID', $this->gameID)->whereIn('EAGUID', $players)->get();

        $this->playerDBLoop($players, $playersDB);
        $this->playerDBLoop($players, $playersDB, 'spectators');
        $this->playerDBLoop($players, $playersDB, 'commander');
        $this->getOnlineAdmins();

        return $this;
    }

    /**
     * Correctly sets the factions name
     * @return this
     */
    private function playerDBLoop($players, $dbPlayers, $type = 'players')
    {
        foreach ($this->data['teams'] as $teamID => $team) {
            if (array_key_exists($type, $team)) {
                foreach ($team[$type] as $index => $player) {
                    if (is_array($player) && array_key_exists('kills', $player) && array_key_exists('deaths', $player)) {
                        $this->data['teams'][$teamID][$type][$index]['kd'] = BattlefieldHelper::kd($player['kills'], $player['deaths']);
                    }

                    foreach ($dbPlayers as $index2 => $player2) {
                        $guid = !is_string($player) ? $player['guid'] : $player;

                        if ($guid == $player2->EAGUID) {
                            if ($type == 'commander') {
                                $index = 0;
                            }

                            if (is_array($player) && array_key_exists('guid', $player)) {
                                if ($player2->GlobalRank != $player['rank']) {
                                    \Queue::push(function ($job) use ($player2, $player) {
                                        $player2->GlobalRank = $player['rank'];
                                        $player2->save();
                                    });
                                }
                            }

                            $this->data['teams'][$teamID][$type][$index]['_player'] = $player2;

                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Gets the players database information
     * @return void
     */
    private function serverinfo()
    {
        $info = $this->client->getServerInfo();
        $this->serverinfo = $info;
        $length = count($info);

        if ($this->gameName == 'BF4') {
            switch ($info[4]) {
                case 'SquadDeathMatch0':
                case 'TeamDeathMatch0':
                    $ticketcap = $length < 28 ? null : intval($info[13]);
                    $uptime    = $length < 28 ? (int) $info[14] : (int) $info[18];
                    $round     = $length < 28 ? (int) $info[15] : (int) $info[19];
                    break;

                case 'CaptureTheFlag0':
                case 'Obliteration':
                case 'Chainlink0':
                case 'RushLarge0':
                case 'Domination0':
                case 'ConquestLarge0':
                case 'ConquestSmall0':
                    if ($info[4] == 'CaptureTheFlag0') {
                        $ticketcap = null;
                    } else {
                        $ticketcap = $length < 26 ? null : intval($info[11]);
                    }

                    $uptime = $length < 26 ? (int) $info[14] : (int) $info[16];
                    $round  = $length < 26 ? (int) $info[15] : (int) $info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime    = -1;
                    $round     = -1;
                    break;
            }
        } elseif ($this->gameName == 'BF3') {
            switch ($info[4]) {
                case 'SquadDeathMatch0':
                case 'TeamDeathMatch0':
                    $ticketcap = $length < 25 ? null : intval($info[11]);
                    $uptime    = $length < 25 ? (int) $info[12] : (int) $info[16];
                    $round     = $length < 25 ? (int) $info[13] : (int) $info[17];
                    break;

                case 'CaptureTheFlag0':
                case 'Obliteration':
                case 'Chainlink0':
                case 'RushLarge0':
                case 'Domination0':
                case 'ConquestLarge0':
                case 'ConquestSmall0':
                    if ($info[4] == 'CaptureTheFlag0') {
                        $ticketcap = null;
                    } else {
                        $ticketcap = $length < 25 ? null : intval($info[11]);
                    }

                    $uptime = $length < 25 ? (int) $info[12] : (int) $info[16];
                    $round  = $length < 25 ? (int) $info[13] : (int) $info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime    = -1;
                    $round     = -1;
                    break;
            }
        } elseif ($this->gameName == 'BFHL') {
            switch ($info[4]) {
                case 'TurfWarLarge0':
                case 'TurfWarSmall0':
                case 'Heist0':
                case 'Hotwire0':
                case 'BloodMoney0':
                case 'Hit0':
                case 'Hostage0':
                case 'TeamDeathMatch0':
                    $ticketcap = $length < 25 ? null : intval($info[11]);
                    $uptime    = $length < 25 ? (int) $info[14] : (int) $info[16];
                    $round     = $length < 25 ? (int) $info[15] : (int) $info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime    = -1;
                    $round     = -1;
                    break;
            }
        }

        if (method_exists($this->client, 'adminVarGetRoundTimeLimit')) {
            $startingTimer = BattlefieldHelper::roundStartingTimer($info[4], $this->client->adminVarGetRoundTimeLimit(), $this->gameName);
        } else {
            $startingTimer = 0;
        }

        if (method_exists($this->client, 'adminVarGetGameModeCounter')) {
            $startingTickets = BattlefieldHelper::startingTickets($info[4], $this->client->adminVarGetGameModeCounter(), $this->gameName);
        } else {
            $startingTickets = 0;
        }

        $this->data['server'] = [
            'name'             => $info[1],
            'description'      => trim($this->client->adminVarGetServerDescription()),
            'type'             => method_exists($this->client, 'adminVarGetServerType') ? $this->client->adminVarGetServerType() : null,
            'isNoobOnly'       => method_exists($this->client, 'adminVarGetNoobJoin') ? $this->client->adminVarGetNoobJoin() : null,
            'game'             => $this->server->game,
            'players'          => [
                'online'     => (int) $info[2],
                'max'        => (int) $info[3],
                'spectators' => 0,
                'commanders' => 0,
                'queue'      => $this->server->in_queue
            ],
            'mode'             => [
                'name' => head($this->client->getPlaymodeName($info[4])),
                'uri'  => $info[4]
            ],
            'map'              => [
                'name'   => head($this->client->getMapName($info[5])),
                'uri'    => $info[5],
                'next'   => $this->getNextMap(),
                'images' => $this->server->map_image_paths
            ],
            'tickets_needed'   => $ticketcap,
            'tickets_starting' => $startingTickets,
            'round_duration'   => $startingTimer,
            'times'            => [
                'round'     => [
                    'humanize' => MainHelper::secToStr($round, true),
                    'seconds'  => (int) $round
                ],
                'uptime'    => [
                    'humanize' => MainHelper::secToStr($uptime, true),
                    'seconds'  => (int) $uptime
                ],
                'remaining' => [
                    'humanize' => $info[2] >= 4 ? MainHelper::secToStr($startingTimer - $round, true) : 'PreMatch',
                    'seconds'  => $startingTimer - $round
                ]
            ]
        ];

        $this->setFactions();

        return $this;
    }

    /**
     * Checks the player list for admins currently in-game.
     *
     * @return void
     */
    private function setFactions()
    {
        if (!$this->check()) {
            throw new \Exception('Setting team factions requires RCON login.');
        }

        if (method_exists($this->client, 'adminVarGetTeamFaction')) {
            $teamFactions = $this->client->adminVarGetTeamFaction(null);
        } else {
            $teamFactions[0] = Lang::get('scoreboard.factions');
        }

        if (in_array($this->data['server']['mode']['uri'], ['SquadDeathMatch0'])) {
            $this->TEAM1 = 'Alpha';
            $this->TEAM2 = 'Bravo';
            $this->TEAM3 = 'Charlie';
            $this->TEAM4 = 'Delta';
        } elseif ($this->data['server']['mode']['uri'] == 'RushLarge0') {
            $this->TEAM1 = $teamFactions[0][4];
            $this->TEAM2 = $teamFactions[0][5];
        } else {
            if ($this->gameName == 'BF3') {
                $this->TEAM0 = $teamFactions[0][0];
                $this->TEAM1 = $teamFactions[0][1];
                $this->TEAM2 = $teamFactions[0][2];
                $this->TEAM3 = $teamFactions[0][1];
                $this->TEAM4 = $teamFactions[0][2];
            } elseif ($this->gameName == 'BFHL') {
                $this->TEAM1 = 'Cops';
                $this->TEAM2 = 'Criminals';
            } else {
                $this->TEAM0 = $teamFactions[0][0];
                $this->TEAM1 = $teamFactions[0][$teamFactions[1][1] + 1];
                $this->TEAM2 = $teamFactions[0][$teamFactions[1][2] + 1];
                $this->TEAM3 = $teamFactions[0][$teamFactions[1][3] + 1];
                $this->TEAM4 = $teamFactions[0][$teamFactions[1][4] + 1];
            }
        }

        return $this;
    }

    /**
     * Function to handle assigning of the DB player object to the playerlist
     * Only used by the getPlayerDBData() function
     *
     * @param  array  $players
     * @param  object $dbPlayers
     * @param  string $type      Valid types are players, spectators, and commander
     * @return void
     */
    public function teams()
    {
        $players = $this->client->tabulate($this->client->adminGetPlayerlist())['players'];

        $temp = [];

        $lockedSquads = [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];

        foreach ($players as $player) {
            $teamID    = $player['teamId'];
            $squadID   = $player['squadId'];
            $squadName = BattlefieldHelper::squad($squadID);

            if (array_key_exists($squadName, $lockedSquads[$teamID]) !== true) {
                $lockedSquads[$teamID][$squadName] = $this->client->adminIsSquadPrivate($teamID, $squadID);
            }

            $additional = [
                'isSquadLocked' => array_key_exists($squadName, $lockedSquads[$teamID]) !== false ? $lockedSquads[$teamID][$squadName] : null,
                'squadName'     => $squadName
            ];

            switch ($teamID) {
                case 0:
                    $score    = null;
                    break;

                case 1:
                    $score    = (int) $this->serverinfo[9];
                    break;

                case 2:
                    $score    = (int) $this->serverinfo[10];
                    break;

                case 3:
                    $score    = (int) $this->serverinfo[11];
                    break;

                case 4:
                    $score    = (int) $this->serverinfo[12];
                    break;
            }

            $teamName = $this->getTeamName($teamID);

            $temp[$teamID]['team'] = $teamName;

            $serverInfoLength = count($this->serverinfo);

            if (($serverInfoLength >= 26 && $serverInfoLength <= 28 && in_array($this->gameName, ['BF4', 'BFHL'])) || ($serverInfoLength == 25 && $this->gameName == 'BF3')) {
                $temp[$teamID]['score'] = $score;
            } else {
                $temp[$teamID]['score'] = 0;
            }

            if (array_key_exists('ping', $player) && $player['ping'] == 65535) {
                $player['ping'] = null;
            }

            switch (array_key_exists('type', $player) ? $player['type'] : 0) {
                case 1:
                    $temp[$teamID]['spectators'][] = $player;
                    $this->data['server']['players']['spectators']++;
                    break;

                case 2:
                case 3:
                    $temp[$teamID]['commander'] = $player;
                    $this->data['server']['players']['commanders']++;
                    break;

                default:
                    $temp[$teamID]['players'][] = array_merge($player, $additional);
            }
        }

        $this->data['lockedSquads'] = $lockedSquads;
        $this->data['teams']        = $temp;
        $this->getPlayerDBData();

        return $this;
    }

    /*-----  End of General Server Commands  ------*/

    protected function getTeamName($teamID)
    {
        switch ($teamID) {
            case 0:
                $teamName = $this->TEAM0;
                break;

            case 1:
                $teamName = $this->TEAM1;
                break;

            case 2:
                $teamName = $this->TEAM2;
                break;

            case 3:
                $teamName = $this->TEAM3;
                break;

            case 4:
                $teamName = $this->TEAM4;
                break;

            default:
                $teamName = null;
        }

        return $teamName;
    }

    /**
     * Checks if the player name is valid
     *
     * Only alphanumeric, dash, and underscore are allowed
     *
     * @param  string  $player Name of player
     * @return boolean
     */
    protected function isValidName($player)
    {
        return preg_match('/^[a-zA-Z0-9_\\-]+$/', $player);
    }

    /**
     * Added the raw information from the game server.
     * Used for debugging
     */
    private function verbose()
    {
        $serverinfo = $this->serverinfo;

        $this->data['_raw']['playerlist'] = $this->client->adminGetPlayerlist();

        for ($i = 0; $i < count($serverinfo); $i++) {
            $key                                    = 'K' . $i;
            $this->data['_raw']['serverinfo'][$key] = $serverinfo[$i];
            if (is_numeric($this->data['_raw']['serverinfo'][$key])) {
                $this->data['_raw']['serverinfo'][$key] = intval($this->data['_raw']['serverinfo'][$key]);
            } else {
                if ($this->data['_raw']['serverinfo'][$key] == 'true' || $this->data['_raw']['serverinfo'][$key] == 'false') {
                    $this->data['_raw']['serverinfo'][$key] = ($this->data['_raw']['serverinfo'][$key] == 'true' ? true : false);
                }
            }
        }

        return $this;
    }
}
