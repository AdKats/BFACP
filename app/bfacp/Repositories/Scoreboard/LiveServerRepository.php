<?php namespace BFACP\Repositories\Scoreboard;

use BFACP\Adkats\Command;
use BFACP\Adkats\Record;
use BFACP\Adkats\Setting;
use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Player;
use BFACP\Battlefield\Server\Server;
use BFACP\Exceptions\PlayerNotFoundException;
use BFACP\Exceptions\RconException;
use BFACP\Facades\Battlefield as BattlefieldHelper;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class LiveServerRepository extends BaseRepository
{
    /**
     * @var Server|null
     */
    public $server = null;

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
     * Server ID
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
     * Formatted Response
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var BF3Conn|BF4Conn|BFHConn|null
     */
    protected $client = null;

    /**
     * Tells us if we've successfully connected to the server.
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * Tell us if we are logged in.
     *
     * @var bool
     */
    protected $authenticated = false;

    /**
     * Holds the server information block
     *
     * @var array
     */
    protected $serverinfo = [];

    /**
     * Stores the admin player object
     *
     * @var Player|null
     */
    protected $admin = null;

    /**
     * Neutral Team
     *
     * @var string
     */
    private $TEAM0 = 'Neutral';

    /**
     * Team 1 Name
     *
     * @var string
     */
    private $TEAM1 = 'US Army';

    /**
     * Team 2 Name
     *
     * @var string
     */
    private $TEAM2 = 'RU Army';

    /**
     * Team 3 Name
     *
     * @var string
     */
    private $TEAM3 = 'US Army';

    /**
     * Team 4 Name
     *
     * @var string
     */
    private $TEAM4 = 'RU Army';

    public function __construct(Server $server)
    {
        parent::__construct();

        $this->server = $server;
        $this->gameID = $server->game->GameID;
        $this->gameName = $server->game->Name;
        $this->serverID = $server->ServerID;
        $this->serverIP = $server->ip;
        $this->port = $server->port;

        if (!is_null($this->user)) {
            $this->setAdmin();
        }
    }

    /**
     * Sets the admin for future use
     */
    private function setAdmin()
    {
        $this->admin = MainHelper::getAdminPlayer($this->user, $this->gameID);
    }

    /**
     * Attempt to establish connection and login to the gameserver.
     *
     * @return $this
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
                    false,
                ]);
                break;

            case 'BF4':
                $this->client = App::make('BFACP\Libraries\BF4Conn', [
                    $this->server,
                    false,
                ]);
                break;

            case 'BFHL':
                $this->client = App::make('BFACP\Libraries\BFHConn', [
                    $this->server,
                    false,
                ]);
                break;

            default:
                throw new RconException(500, sprintf('Unsupported game %s', $this->gameName));
        }

        // Update connection state
        $this->connected = $this->client->isConnected();

        // If we are not connected throw exception and abort
        if (!$this->connected) {
            throw new RconException(410, Lang::get('system.exceptions.rcon.conn_failed'));
        }

        if (is_null($this->server->setting)) {
            throw new RconException(500, Lang::get('system.exceptions.rcon.not_configured'));
        }

        // Attempt to login with provided RCON password
        $this->client->loginSecure($this->server->setting->getPassword());

        // Update authentication state
        $this->authenticated = $this->client->isLoggedIn();

        // If we are connected but not logged in throw exception and abort
        if (!$this->authenticated) {
            throw new RconException(401, Lang::get('system.exceptions.rcon.bad_password'));
        }

        $this->serverinfo();

        return $this;
    }

    /**
     * Gathers the server information
     *
     * @return $this
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
                    $uptime = $length < 28 ? (int)$info[14] : (int)$info[18];
                    $round = $length < 28 ? (int)$info[15] : (int)$info[19];
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

                    $uptime = $length < 26 ? (int)$info[14] : (int)$info[16];
                    $round = $length < 26 ? (int)$info[15] : (int)$info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime = -1;
                    $round = -1;
                    break;
            }
        } elseif ($this->gameName == 'BF3') {
            switch ($info[4]) {
                case 'SquadDeathMatch0':
                case 'TeamDeathMatch0':
                    $ticketcap = $length < 25 ? null : intval($info[11]);
                    $uptime = $length < 25 ? (int)$info[12] : (int)$info[16];
                    $round = $length < 25 ? (int)$info[13] : (int)$info[17];
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

                    $uptime = $length < 25 ? (int)$info[12] : (int)$info[16];
                    $round = $length < 25 ? (int)$info[13] : (int)$info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime = -1;
                    $round = -1;
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
                case 'CashGrab0':
                    $ticketcap = $length < 25 ? null : intval($info[11]);
                    $uptime = $length < 25 ? (int)$info[14] : (int)$info[16];
                    $round = $length < 25 ? (int)$info[15] : (int)$info[17];
                    break;

                default:
                    $ticketcap = null;
                    $uptime = -1;
                    $round = -1;
                    break;
            }
        }

        if (method_exists($this->client, 'adminVarGetRoundTimeLimit')) {
            $startingTimer = BattlefieldHelper::roundStartingTimer($info[4], $this->client->adminVarGetRoundTimeLimit(),
                $this->gameName);
        } else {
            $startingTimer = 0;
        }

        if (method_exists($this->client, 'adminVarGetGameModeCounter')) {
            $startingTickets = BattlefieldHelper::startingTickets($info[4], $this->client->adminVarGetGameModeCounter(),
                $this->gameName);
        } else {
            $startingTickets = 0;
        }

        if ($this->isLoggedIn) {
            $presetMessages = Setting::servers($this->serverID)->settings('Pre-Message List')->first();
        }

        $_playmode = $this->client->getPlaymodeName($info[4]);
        $_map = $this->client->getMapName($info[5]);

        $this->data['server'] = [
            'name'             => $info[1],
            'description'      => trim($this->client->adminVarGetServerDescription()),
            'type'             => method_exists($this->client,
                'adminVarGetServerType') ? $this->client->adminVarGetServerType() : null,
            'isNoobOnly'       => method_exists($this->client,
                'adminVarGetNoobJoin') ? $this->client->adminVarGetNoobJoin() : null,
            'game'             => $this->server->game,
            'players'          => [
                'online'     => (int)$info[2],
                'max'        => (int)$info[3],
                'spectators' => 0,
                'commanders' => 0,
                'queue'      => $this->server->in_queue,
            ],
            'mode'             => [
                'name' => !is_string($_playmode) ? head($_playmode) : $_playmode,
                'uri'  => $info[4],
            ],
            'map'              => [
                'name'   => !is_string($_map) ? head($_map) : $_map,
                'uri'    => $info[5],
                'next'   => $this->getNextMap(),
                'images' => $this->server->map_image_paths,
            ],
            'tickets_needed'   => $ticketcap,
            'tickets_starting' => $startingTickets,
            'round_duration'   => $startingTimer,
            'times'            => [
                'round'     => [
                    'humanize' => MainHelper::secToStr($round, true),
                    'seconds'  => (int)$round,
                ],
                'uptime'    => [
                    'humanize' => MainHelper::secToStr($uptime, true),
                    'seconds'  => (int)$uptime,
                ],
                'remaining' => [
                    'humanize' => $info[2] >= 4 ? MainHelper::secToStr($startingTimer - $round, true) : 'PreMatch',
                    'seconds'  => $info[2] >= 4 ? $startingTimer - $round : $startingTimer,
                ],
            ],
        ];

        $this->setFactions();

        if (isset($presetMessages)) {
            if (is_array($presetMessages->setting_value)) {
                $this->data['_presetmessages'] = array_merge([''], $presetMessages->setting_value);
            } else {
                $this->data['_presetmessages'][0] = $presetMessages->setting_value;
            }
        } else {
            $this->data['_presetmessages'] = [];
        }

        $this->data['_teams'] = [
            [
                'id'    => 1,
                'label' => sprintf('%s (%s)', $this->TEAM1['full_name'], 'Team 1'),
            ],
            [
                'id'    => 2,
                'label' => sprintf('%s (%s)', $this->TEAM2['full_name'], 'Team 2'),
            ],
            [
                'id'    => 3,
                'label' => sprintf('%s (%s)', $this->TEAM3['full_name'], 'Team 3'),
            ],
            [
                'id'    => 4,
                'label' => sprintf('%s (%s)', $this->TEAM4['full_name'], 'Team 4'),
            ],
        ];

        return $this;
    }

    /**
     * Gets the next map in the rotation
     *
     * @return array|null
     */
    private function getNextMap()
    {
        $index = $this->client->adminMaplistGetNextMapIndex();
        $maps = $this->getMapList();

        foreach ($maps as $map) {
            if ($map['index'] == $index) {
                return $map;
            }

        }

        return null;
    }

    /**
     * Gets the map list
     *
     * @return array
     */
    private function getMapList()
    {
        $maps = $this->client->adminMaplistList();

        $list = [];

        for ($i = 0; $i < $maps[1]; $i++) {
            $map = $maps[ ($maps[2]) * $i + $maps[2] ];
            $mode = $maps[ ($maps[2]) * $i + $maps[2] + 1 ];
            $rounds = $maps[ ($maps[2]) * $i + $maps[2] + 2 ];

            $_playmode = $this->client->getPlaymodeName($mode);
            $_map = $this->client->getMapName($map);

            $list[] = [
                'map'    => [
                    'name' => !is_string($_map) ? head($_map) : $_map,
                    'uri'  => $map,
                ],
                'mode'   => [
                    'name' => !is_string($_playmode) ? head($_playmode) : $_playmode,
                    'uri'  => $mode,
                ],
                'rounds' => (int)$rounds,
                'index'  => $i,
            ];
        }

        return $list;
    }

    /**
     * Sets the correct team factions based on game and mode
     *
     * @return $this
     * @throws RconException
     */
    private function setFactions()
    {
        if (!$this->check()) {
            throw new RconException(500, Lang::get('system.exceptions.rcon.factions'));
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
            $this->TEAM3 = $teamFactions[0][4];
            $this->TEAM4 = $teamFactions[0][5];
        } else {
            if ($this->gameName == 'BF3') {
                $this->TEAM0 = $teamFactions[0][0];
                $this->TEAM1 = $teamFactions[0][1];
                $this->TEAM2 = $teamFactions[0][2];
                $this->TEAM3 = $teamFactions[0][1];
                $this->TEAM4 = $teamFactions[0][2];
            } elseif ($this->gameName == 'BFHL') {
                $this->TEAM1 = $teamFactions[0][6];
                $this->TEAM2 = $teamFactions[0][7];
                $this->TEAM3 = $teamFactions[0][6];
                $this->TEAM4 = $teamFactions[0][7];
            } else {
                $this->TEAM0 = $teamFactions[0][0];
                $this->TEAM1 = $teamFactions[0][ $teamFactions[1][1] + 1 ];
                $this->TEAM2 = $teamFactions[0][ $teamFactions[1][2] + 1 ];
                $this->TEAM3 = $teamFactions[0][ $teamFactions[1][3] + 1 ];
                $this->TEAM4 = $teamFactions[0][ $teamFactions[1][4] + 1 ];
            }
        }

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

    /**
     * Checks if the player name is valid. Only alphanumeric, dash, and underscore are allowed.
     *
     * @param $player
     *
     * @return bool
     */
    protected function isValidName($player)
    {
        return (bool)preg_match('/^[a-zA-Z0-9_\\-]+$/', $player);
    }

    /*======================================
    =            Admin Commands            =
    ======================================*/

    /**
     * Kills the players
     *
     * @param string $player
     * @param string $message
     *
     * @return bool
     */
    public function adminKill($player = '', $message = '')
    {
        // Save the original message for use later.
        $originalMessage = $message;

        if (!empty($message)) {
            $message = sprintf('You were killed by an admin. Reason: %s', $message);
        } else {
            $message = 'You were killed by an admin.';
        }

        if ($this->isValidName($player)) {
            $response = $this->client->adminKillPlayer($player);

            // Check if the server returned a player not found error
            if ($response == 'InvalidPlayerName') {
                throw new PlayerNotFoundException(404, sprintf('No player found with the name "%s"', $player));
            }

            if ($response == 'SoldierNotAlive') {
                throw new PlayerNotFoundException(404, 'Player was already dead.');
            }

            $this->adminTell($player, $message, 5, false, true, 1);
            $this->log($player, 'player_kill', $originalMessage);
        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return [
            'player'  => $player,
            'message' => $originalMessage,
        ];
    }

    /**
     * Nukes the targeted team
     *
     * @param int $team
     *
     * @return bool
     */
    public function adminNuke($team = 0)
    {
        $players = $this->client->tabulate($this->client->adminGetPlayerlist())['players'];

        $teamName = $this->getTeamName($team);

        $message = sprintf('NUKE issued on team %s', $teamName['full_name']);

        foreach ($players as $player) {
            if ($player['teamId'] == $team) {
                $this->client->adminKillPlayer($player['name']);
                $this->adminTell($player['name'], $message, 5, false, true, 1);
            }
        }

        $this->log($teamName['full_name'], 'server_nuke', sprintf('Nuke Server (%s)', $teamName['full_name']));

        return true;
    }

    /**
     * Sends both a yell and say message to the player
     *
     * @param string     $player
     * @param string     $message
     * @param int        $yellDuration
     * @param bool|true  $displayAdminName
     * @param bool|false $skipLog
     * @param int        $times
     *
     * @return bool
     */
    public function adminTell(
        $player = '',
        $message = '',
        $yellDuration = 10,
        $displayAdminName = true,
        $skipLog = false,
        $times = 7
    ) {
        for ($i = 0; $i < $times; $i++) {
            $this->adminSay($message, $player, null, 'Player', $displayAdminName, true);
        }

        $this->adminYell($message, $player, null, $yellDuration, 'Player', true);

        if (!$skipLog) {
            $this->log($player, 'player_tell', $message);
        }

        return true;
    }

    /**
     * Sends a say to the entire server, team, or player.
     *
     * @param string      $message
     * @param null|string $player
     * @param null|string $teamId
     * @param string      $type
     * @param bool|true   $displayAdminName
     * @param bool|false  $skipLog
     *
     * @return bool
     */
    public function adminSay(
        $message = '',
        $player = '',
        $teamId = '',
        $type = 'All',
        $displayAdminName = true,
        $skipLog = false
    ) {
        // Checks if the message is blank
        if (empty($message)) {
            throw new RconException(400, 'No message provided.');
        }

        $adminName = is_null($this->admin) ? $this->user->username : $this->admin->SoldierName;

        switch ($type) {
            case 'All':

                if ($displayAdminName) {
                    $this->client->adminSayMessageToAll(sprintf('[%s] %s', $adminName, $message));
                } else {
                    $this->client->adminSayMessageToAll($message);
                }

                if (!$skipLog) {
                    return $this->log(null, 'admin_say', $message);
                }

                break;

            case 'Team':
                // Checks if a team id was sent
                if (empty($teamId)) {
                    throw new RconException(400, 'No team id specified.');
                }

                // Checks if the team id is a number or not in valid id list
                if (!is_numeric($teamId) || !in_array($teamId, [1, 2, 3, 4])) {
                    throw new RconException(400, sprintf('"%s" is not a valid team id', $teamId));
                }

                if ($displayAdminName) {
                    $this->client->adminSayMessageToTeam($teamId, sprintf('[%s] %s', $adminName, $message));
                } else {
                    $this->client->adminSayMessageToTeam($teamId, $message);
                }
                break;

            case 'Player':
                // Remove extra whitespace
                $player = trim($player);

                // Checks if name provided is blank
                if (empty($player)) {
                    throw new RconException(400, 'No player name specified.');
                }

                if ($this->isValidName($player)) {

                    if ($displayAdminName) {
                        $response = $this->client->adminSayMessageToPlayer($player,
                            sprintf('[%s] %s', $adminName, $message));
                    } else {
                        $response = $this->client->adminSayMessageToPlayer($player, $message);
                    }

                    // Check if the server returned a player not found error
                    if ($response == 'PlayerNotFound') {
                        throw new PlayerNotFoundException(404, sprintf('No player found with the name "%s"', $player));
                    }

                    if (!$skipLog) {
                        return $this->log($player, 'player_say', $message);
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
     * Sends a yell to the entire server, team, or player.
     *
     * @param string      $message
     * @param null|string $player
     * @param int|null    $teamId
     * @param int         $duration
     * @param string      $type
     * @param bool|false  $skipLog
     *
     * @return bool
     */
    public function adminYell(
        $message = '',
        $player = '',
        $teamId = 0,
        $duration = 5,
        $type = 'All',
        $skipLog = false
    ) {
        // Checks if the message is blank
        if (empty($message)) {
            throw new RconException(400, 'No message provided.');
        }

        if ($this->gameName == 'BFHL') {
            $message = str_limit($message, 100);
        }

        switch ($type) {
            case 'All':
                $this->client->adminYellMessage($message, '{%all%}', $duration);

                if (!$skipLog) {
                    $this->log(null, 'admin_yell', $message, $duration);
                }

                break;

            case 'Team':
                // Checks if a team id was sent
                if (empty($teamId)) {
                    throw new RconException(400, 'No team id specified.');
                }

                // Checks if the team id is a number or not in valid id list
                if (!is_numeric($teamId) || !in_array($teamId, [1, 2, 3, 4])) {
                    throw new RconException(400, sprintf('"%s" is not a valid team id', $teamId));
                }

                $this->client->adminYellMessageToTeam($message, $teamId, $duration);
                break;

            case 'Player':
                // Remove extra whitespace
                $player = trim($player);

                // Checks if name provided is blank
                if (empty($player)) {
                    throw new RconException(400, 'No player name specified.');
                }

                if ($this->isValidName($player)) {
                    $response = $this->client->adminYellMessage($message, $player, $duration);

                    // Check if the server returned a player not found error
                    if ($response == 'PlayerNotFound') {
                        throw new PlayerNotFoundException(404, sprintf('No player found with the name "%s"', $player));
                    }

                    if (!$skipLog) {
                        $record = $this->log($player, 'player_yell', $message, $duration);

                        return $record['record'];
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
     * Moves the player to a different team and/or squad
     *
     * @param            $player
     * @param null       $teamId
     * @param int        $squadId
     * @param bool|false $locked
     *
     * @return bool
     * @throws PlayerNotFoundException|RconException
     */
    public function adminMovePlayer($player, $teamId = null, $squadId = 0, $locked = false)
    {
        if (!is_numeric($squadId) || empty($squadId) || !in_array($squadId, range(0, 32))) {
            $squadId = 0;
        }

        if (!is_numeric($teamId) || empty($teamId) || !in_array($teamId, range(1, 4))) {
            $teamId = $this->client->getPlayerTeamId($player);
        }

        $teamName = $this->getTeamName($teamId);
        $squadName = BattlefieldHelper::squad($squadId);

        if (is_array($teamName)) {
            $teamName = $teamName['full_name'];
        }

        if ($this->isValidName($player)) {
            if (method_exists($this->client, 'adminGetSquadPrivate') && method_exists($this->client,
                    'adminSetSquadPrivate')
            ) {
                // Check if squad is private
                if ($squadId != 0 && $this->client->adminGetSquadPrivate($teamId, $squadId)) {
                    // Check if squad is full
                    $playersInSquad = $this->client->adminSquadListPlayer($teamId, $squadId)[1];

                    // If squad is full throw an exception with an error message
                    // else unlock the squad so we can move them in.
                    if ($playersInSquad == 5) {
                        throw new RconException(200,
                            sprintf('%s squad is full. Cannot switch %s to squad.', $squadName, $player));
                    } else {
                        $this->client->adminSetSquadPrivate($teamId, $squadId, false);
                    }
                }
            }

            $response = $this->client->adminMovePlayerSwitchSquad($player, (int)$squadId, true, (int)$teamId);

            // Check if the server returned a player not found error
            if ($response == 'InvalidPlayerName') {
                throw new PlayerNotFoundException(404, sprintf('No player found with the name "%s"', $player));
            }

            // Lock squad if $locked is truthy
            if (MainHelper::stringToBool($locked)) {
                $this->client->adminSetSquadPrivate($teamId, $squadId, $locked);
            }

            if ($response == 'SetSquadFailed') {
                $squadId = 0;
            }

            if ($squadId == 0) {
                $message = sprintf('You were switched to team %s and not placed in a squad.', $teamName);
            } else {
                $message = sprintf('You were switched to team %s and placed in squad %s.', $teamName, $squadName);
            }

            $dbMessage = sprintf('Switched to %s and placed in squad %s', $teamName, $squadName);

            $this->adminTell($player, $message, 5, false, true, 1);
            $this->log($player, 'player_fmove', $dbMessage);

        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return [
            'player'  => $player,
            'message' => $dbMessage,
        ];
    }

    /**
     * Kick the player from the server
     *
     * @param string     $player
     * @param null       $message
     * @param bool|false $isBan
     *
     * @return bool
     * @throws PlayerNotFoundException|RconException
     */
    public function adminKick($player = '', $message = null, $isBan = false)
    {
        if (empty($message)) {
            $message = 'Kicked by administrator';
        }

        if ($this->isValidName($player)) {
            $response = $this->client->adminKickPlayerWithReason($player, $message);

            // Check if the server returned a player not found error
            if (in_array($response, ['PlayerNotFound', 'InvalidPlayerName'])) {
                throw new PlayerNotFoundException(404, sprintf('No player found with the name "%s"', $player));
            }

            // If adminKick was called from the adminBan function do not send the kick message
            if (!$isBan) {
                // Send a general message to the server about the kicked player
                $this->adminSay(sprintf('%s was kicked from the server. Reason: %s', $player, $message), null, null,
                    'All', false, true);
            }

            $this->log($player, 'player_kick', $message);
        } else {
            throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
        }

        return [
            'player'  => $player,
            'message' => $message,
        ];
    }

    /**
     * Punish player
     *
     * @param $player
     * @param $message
     *
     * @return array
     * @throws RconException
     * @throws PlayerNotFoundException
     */
    public function adminPunish($player, $message)
    {
        if ($this->isValidName($player)) {
            $p = Player::where('GameID', $this->gameID)->where('SoldierName', $player)->first();

            if (!$p) {
                throw new PlayerNotFoundException(404, 'Unable to punish. %s was not found.', $player);
            }

            if (empty($message)) {
                throw new RconException(400, 'No reason provided');
            }

            return [
                'player'  => $player,
                'message' => $message,
                'record'  => $this->log($player, 'player_punish', $message, 0, false),
            ];
        }

        throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
    }

    /**
     * Forgive player
     *
     * @param  string  $player  Name of player
     * @param  string  $message Message to be sent
     * @param  integer $count   How many forgives should be issued
     *
     * @return array
     * @throws RconException
     * @throws PlayerNotFoundException
     */
    public function adminForgive($player, $message, $count = 1)
    {
        if ($this->isValidName($player)) {
            $p = Player::where('GameID', $this->gameID)->where('SoldierName', $player)->first();

            if (!$p) {
                throw new PlayerNotFoundException(404, 'Unable to forgive. %s was not found.', $player);
            }

            if (empty($message)) {
                throw new RconException(400, 'No reason provided');
            }

            return [
                'player'  => $player,
                'message' => $message,
                'record'  => $this->log($player, 'player_forgive', $message, 0, false),
            ];
        }

        throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
    }

    /**
     * Mute player
     *
     * @param  string $player  Name of player
     * @param  string $message Message to be sent
     *
     * @return boolean
     */
    public function adminMute($player, $message)
    {
        if ($this->isValidName($player)) {
            $p = Player::where('GameID', $this->gameID)->where('SoldierName', $player)->first();

            if (!$p) {
                throw new PlayerNotFoundException(404, 'Unable to mute. %s was not found.', $player);
            }

            if (empty($message)) {
                throw new RconException(400, 'No reason provided');
            }

            return [
                'player'  => $player,
                'message' => $message,
                'record'  => $this->log($player, 'player_mute', $message, 0, false),
            ];
        }

        throw new RconException(400, sprintf('"%s" is not a valid name.', $player));
    }

    /*-----  End of Admin Commands  ------*/

    /**
     * Logs action to database
     *
     * @param string|Player $target
     * @param string        $command
     * @param string        $message
     * @param int           $duration
     * @param bool|true     $sys
     *
     * @return \BFACP\Adkats\Record
     */
    private function log($target, $command, $message = 'No Message', $duration = 0, $sys = true)
    {
        $timestamp = Carbon::now();

        $command = Command::where('command_key', $command)->first();

        if (!$command) {
            throw new RconException(500, 'Invalid command type');
        }

        if (!$target instanceof Player && is_string($target) && !in_array($command->command_key, ['server_nuke'])) {
            $target = Player::where('GameID', $this->gameID)->where('SoldierName', $target)->first();
        }

        if ($target instanceof Player) {
            $target_name = $target->SoldierName;
            $target_id = $target->PlayerID;
        } else {
            $target_name = is_null($target) ? 'Server' : $target;
            $target_id = null;
        }

        if ($this->admin instanceof Player) {
            $source_name = $this->admin->SoldierName;
            $source_id = $this->admin->PlayerID;
        } else {
            $source_name = $this->user->username;
            $source_id = null;
        }

        $data = [];

        if ($command->command_key == 'admin_say') {
            $data['chat'] = Chat::create([
                'ServerID'       => $this->serverID,
                'logDate'        => $timestamp,
                'logMessage'     => $message,
                'logPlayerID'    => $source_id,
                'logSoldierName' => $source_name,
                'logSubset'      => 'Global',
            ]);

            if ($this->admin instanceof Player) {
                $data['chat']['player'] = $this->admin;
            }
        }

        $record = new Record();
        $record->adkats_read = $sys ? 'Y' : 'N';
        $record->adkats_web = true;
        $record->command_action = $command->command_id;
        $record->command_type = $command->command_id;
        $record->command_numeric = $duration;
        $record->record_message = $message;
        $record->record_time = $timestamp;
        $record->server_id = $this->serverID;
        $record->source_name = $source_name;
        $record->source_id = $source_id;
        $record->target_name = $target_name;
        $record->target_id = $target_id;
        $record->save();

        $data['record'] = $record;

        return $data;
    }

    /**
     * Simply returns the correct team name by their ID
     *
     * @param $teamID
     *
     * @return null|string
     */
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
     * Returns what's in $this->data
     *
     * @param bool|false $verbose
     *
     * @return array
     */
    public function get($verbose = false)
    {
        if ($verbose) {
            $this->verbose();
        }

        return $this->data;
    }

    /**
     * Added the raw information from the game server. Used for debugging only.
     *
     * @return $this
     */
    private function verbose()
    {
        $serverinfo = $this->serverinfo;

        $this->data['_raw']['playerlist'] = $this->client->adminGetPlayerlist();

        for ($i = 0; $i < count($serverinfo); $i++) {
            $key = 'K' . $i;

            $this->data['_raw']['serverinfo'][ $key ] = $serverinfo[ $i ];

            if (is_numeric($this->data['_raw']['serverinfo'][ $key ])) {
                $this->data['_raw']['serverinfo'][ $key ] = intval($this->data['_raw']['serverinfo'][ $key ]);
            } else {
                if ($this->data['_raw']['serverinfo'][ $key ] == 'true' || $this->data['_raw']['serverinfo'][ $key ] == 'false') {
                    $this->data['_raw']['serverinfo'][ $key ] = ($this->data['_raw']['serverinfo'][ $key ] == 'true' ? true : false);
                }
            }
        }

        $this->data['_raw']['sql_time'] = 0;
        $this->data['_raw']['sql'] = DB::getQueryLog();

        foreach ($this->data['_raw']['sql'] as $sql) {
            $this->data['_raw']['sql_time'] = $this->data['_raw']['sql_time'] + $sql['time'];
        }

        return $this;
    }

    /**
     * Loops over the players and sorts them into teams
     *
     * @return $this
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
            4 => [],
        ];

        foreach ($players as $player) {
            $teamID = $player['teamId'];
            $squadID = $player['squadId'];
            $squadName = BattlefieldHelper::squad($squadID);

            if (array_key_exists($squadName, $lockedSquads[ $teamID ]) !== true) {
                $lockedSquads[ $teamID ][ $squadName ] = $this->client->adminGetSquadPrivate($teamID, $squadID);
            }

            $additional = [
                'isSquadLocked' => array_key_exists($squadName,
                    $lockedSquads[ $teamID ]) !== false ? $lockedSquads[ $teamID ][ $squadName ] : null,
                'squadName'     => $squadName,
            ];

            switch ($teamID) {
                case 0:
                    $score = null;
                    break;

                case 1:
                    $score = (int)$this->serverinfo[9];
                    break;

                case 2:
                    $score = (int)$this->serverinfo[10];
                    break;

                case 3:
                    $score = (int)$this->serverinfo[11];
                    break;

                case 4:
                    $score = (int)$this->serverinfo[12];
                    break;
            }

            $teamName = $this->getTeamName($teamID);

            $temp[ $teamID ]['team'] = $teamName;

            $serverInfoLength = count($this->serverinfo);

            if (($serverInfoLength >= 26 && $serverInfoLength <= 28 && in_array($this->gameName,
                        ['BF4', 'BFHL'])) || ($serverInfoLength == 25 && $this->gameName == 'BF3')
            ) {
                $temp[ $teamID ]['score'] = $score;
            } else {
                $temp[ $teamID ]['score'] = 0;
            }

            if (array_key_exists('ping', $player) && $player['ping'] == 65535) {
                $player['ping'] = null;
            }

            switch (array_key_exists('type', $player) ? $player['type'] : 0) {
                case 1:
                    $temp[ $teamID ]['spectators'][] = $player;
                    $this->data['server']['players']['spectators']++;
                    break;

                case 2:
                case 3:
                    $temp[ $teamID ]['commander'] = $player;
                    $this->data['server']['players']['commanders']++;
                    break;

                default:
                    $temp[ $teamID ]['players'][] = array_merge($player, $additional);
            }
        }

        $this->data['lockedSquads'] = $lockedSquads;
        $this->data['teams'] = $temp;
        $this->getPlayerDBData();

        return $this;
    }

    /**
     * Gets players DB information and passes it to playerDBLoop function
     *
     * @return $this|bool
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

        $this->playerDBLoop($playersDB);
        $this->playerDBLoop($playersDB, 'spectators');
        $this->playerDBLoop($playersDB, 'commander');
        $this->getOnlineAdmins();

        return $this;
    }

    /**
     * Assigns the player with their DB profile
     *
     * @param array  $dbPlayers
     * @param string $type
     *
     * @return $this
     */
    private function playerDBLoop($dbPlayers = [], $type = 'players')
    {
        foreach ($this->data['teams'] as $teamID => $team) {
            if (array_key_exists($type, $team)) {
                foreach ($team[ $type ] as $index => $player) {
                    if (is_array($player) && array_key_exists('kills', $player) && array_key_exists('deaths',
                            $player)
                    ) {
                        $this->data['teams'][ $teamID ][ $type ][ $index ]['kd'] = BattlefieldHelper::kd($player['kills'],
                            $player['deaths']);
                    }

                    foreach ($dbPlayers as $index2 => $player2) {
                        $guid = !is_string($player) ? $player['guid'] : $player;

                        if ($guid == $player2->EAGUID) {
                            if ($type == 'commander') {
                                $index = 0;
                            }

                            if (is_array($player) && array_key_exists('guid', $player)) {
                                $updated = false;

                                // If player rank doesn't match the database update it
                                if ($player2->GlobalRank != $player['rank']) {
                                    if ($player['rank'] > 0) {
                                        $player2->GlobalRank = $player['rank'];

                                        $updated = true;
                                    }
                                }

                                // If player name doesn't match the database update it
                                if ($player2->SoldierName != $player['name']) {
                                    $player2->SoldierName = $player['name'];

                                    $updated = true;
                                }

                                // If player name or rank are changed save changes to database
                                if ($updated) {
                                    $player2->save();
                                }
                            }

                            $this->data['teams'][ $teamID ][ $type ][ $index ]['_player'] = $player2;

                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Check for admins in the player list.
     *
     * @return $this|bool
     */
    private function getOnlineAdmins()
    {
        $adminlist = DB::table('adkats_usersoldiers')->select('player_id', 'EAGUID', 'GameID',
            'SoldierName')->join('adkats_users', 'adkats_usersoldiers.user_id', '=',
            'adkats_users.user_id')->join('adkats_roles', 'adkats_users.user_role', '=',
            'adkats_roles.role_id')->join('tbl_playerdata', 'adkats_usersoldiers.player_id', '=',
            'tbl_playerdata.PlayerID')->where('tbl_playerdata.GameID', $this->gameID)->whereExists(function ($query) {
            $query->select('adkats_rolecommands.role_id')->from('adkats_rolecommands')->join('adkats_commands',
                'adkats_rolecommands.command_id', '=', 'adkats_commands.command_id')->where('command_playerInteraction',
                1)->whereRaw('adkats_rolecommands.role_id = adkats_users.user_role')->groupBy('adkats_rolecommands.role_id');
        })->get();

        foreach (['players', 'spectators', 'commander'] as $type) {
            foreach ($this->data['teams'] as $teamID => $team) {
                if (array_key_exists($type, $team)) {
                    foreach ($team[ $type ] as $index => $player) {
                        foreach ($adminlist as $index2 => $player2) {
                            $guid = !is_string($player) ? $player['guid'] : $player;

                            if ($guid == $player2->EAGUID) {
                                if ($type == 'commander') {
                                    return false;
                                }

                                $this->data['admins'][ $player['name'] ] = $this->data['teams'][ $teamID ][ $type ][ $index ];
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }
}
