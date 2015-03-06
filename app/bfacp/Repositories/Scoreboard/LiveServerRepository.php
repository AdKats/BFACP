<?php namespace BFACP\Repositories\Scoreboard;

use BFACP\AdKats\Setting AS AdKatsSetting;
use BFACP\Battlefield\Server;
use BFACP\Battlefield\Player;
use BFACP\Contracts\Scoreboard;
use BattlefieldHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use MainHelper;

class LiveServerRepository
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
    protected $gameName = NULL;

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
     * Array of pre-defined messages from AdKats
     *
     * @var array
     */
    protected $messages = [];

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
    protected $connected = FALSE;

    /**
     * Tell us if we are logged in.
     *
     * @var boolean
     */
    protected $authenticated = FALSE;

    /**
     * Server Object
     *
     * @var object
     */
    public $server;

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
        $this->server   = $server;
        $this->gameID   = $server->game->GameID;
        $this->gameName = $server->game->Name;
        $this->serverID = $server->ServerID;
        $this->serverIP = $server->ip;
        $this->port     = $server->port;

        if(Auth::check())
        {
            $this->messages = AdKatsSetting::servers([$server->ServerID])->settings(['Pre-Message List'])->first();
            $this->data['_settings']['messages'] = $this->messages->setting_value;
        }
    }

    /**
     * Attempt to establish connection and login to the gameserver.
     *
     * @return this
     */
    public function attempt()
    {
        if( ! is_null($this->client) )
        {
            return $this;
        }

        switch($this->gameName)
        {
            case "BF3":
                $this->client = App::make('BFACP\Libraries\BF3Conn', [
                    $this->server,
                    FALSE
                ]);
            break;

            case "BF4":
                $this->client = App::make('BFACP\Libraries\BF4Conn', [
                    $this->server,
                    FALSE
                ]);
            break;

            default:
                throw new \Exception(sprintf('Unsupported game %s', $this->gameName));
        }

        // Update connection state
        $this->connected = $this->client->isConnected();

        // Attempt to login with provided RCON password
        $this->client->loginSecure( $this->server->setting->getPassword() );

        // Update authentication state
        $this->authenticated = $this->client->isLoggedIn();

        $this->serverinfo()->teams();

        return $this;
    }

    /**
     * Determine if we are connected and authenticated with the gameserver.
     *
     * @return bool
     */
    public function check()
    {
        if( $this->connected && $this->authenticated )
            return TRUE;

        return FALSE;
    }

    /**
     * Loops over the players and sorts them into teams
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function teams()
    {
        $this->setFactions();

        $players = $this->client->tabulate( $this->client->adminGetPlayerlist() )['players'];

        $temp = [];

        $lockedSquads = [
            0 => [],
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];

        foreach($players as $player)
        {
            $teamID = $player['teamId'];
            $squadID = $player['squadId'];
            $squadName = BattlefieldHelper::squad($squadID);

            if( array_key_exists($squadName, $lockedSquads[$teamID]) !== TRUE )
                $lockedSquads[$teamID][$squadName] = $this->client->adminIsSquadPrivate($teamID, $squadID);

            $additional = [
                'isSquadLocked' => array_key_exists($squadName, $lockedSquads[$teamID]) !== FALSE ? $lockedSquads[$teamID][$squadName] : NULL,
                'squadName' => $squadName,
            ];

            switch($teamID)
            {
                case 0:
                    $teamName = $this->TEAM0;
                    $score = NULL;
                break;

                case 1:
                    $teamName = $this->TEAM1;
                    $score = (int) $this->serverinfo[9];
                break;

                case 2:
                    $teamName = $this->TEAM2;
                    $score = (int) $this->serverinfo[10];
                break;

                case 3:
                    $teamName = $this->TEAM3;
                    $score = (int) $this->serverinfo[11];
                break;

                case 4:
                    $teamName = $this->TEAM4;
                    $score = (int) $this->serverinfo[12];
                break;

                default:
                    $teamName = NULL;
            }

            $temp[$teamID]['team'] = $teamName;

            if(count($this->serverinfo) >= 26 && count($this->serverinfo) <= 28)
                $temp[$teamID]['score'] = $score;
            else
                $temp[$teamID]['score'] = 0;

            switch($player['type'])
            {
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
        $this->data['teams'] = $temp;
        $this->getPlayerDBData();

        return $this;
    }

    /**
     * Gathers the server information and puts them in the server array
     * @return this
     */
    private function serverinfo()
    {
        $info = $this->client->getServerInfo();

        $this->serverinfo = $info;

        $length = count($info);

        switch($info[4])
        {
            case "SquadDeathMatch0":
            case "TeamDeathMatch0":
                $ticketcap = $length < 28 ? NULL : intval($info[13]);
                $uptime    = $length < 28 ? (int) $info[14] : (int) $info[18];
                $round     = $length < 28 ? (int) $info[15] : (int) $info[19];
            break;

            case "CaptureTheFlag0":
            case "Obliteration":
            case "Chainlink0":
            case "RushLarge0":
            case "Domination0":
            case "ConquestLarge0":
            case "ConquestSmall0":
                if($info[4] == 'CaptureTheFlag0')
                    $ticketcap = NULL;
                else
                    $ticketcap = $length < 26 ? NULL : intval($info[11]);

                $uptime    = $length < 26 ? (int) $info[14] : (int) $info[16];
                $round     = $length < 26 ? (int) $info[15] : (int) $info[17];
            break;

            default:
                $ticketcap = NULL;
                $uptime    = NULL;
                $round     = NULL;
            break;
        }

        $startingTimer   = BattlefieldHelper::roundStartingTimer($info[4], $this->client->adminVarGetRoundTimeLimit(), $this->gameName);
        $startingTickets = BattlefieldHelper::startingTickets($info[4], $this->client->adminVarGetGameModeCounter(), $this->gameName);

        $this->data['server'] = [
            'name' => $info[1],
            'description' => trim($this->client->adminVarGetServerDescription()),
            'type' => $this->client->adminVarGetServerType(),
            'isNoobOnly' => $this->client->adminVarGetNoobJoin(),
            'game' => $this->server->game,
            'players' => [
                'online' => (int) $info[2],
                'max' => (int) $info[3],
                'spectators' => 0,
                'commanders' => 0,
                'queue' => $this->server->in_queue
            ],
            'mode' => [
                'name' => head($this->client->getPlaymodeName( $info[4] )),
                'uri' => $info[4]
            ],
            'map' => [
                'name' => head($this->client->getMapName( $info[5] )),
                'uri'  => $info[5],
                'next' => $this->getNextMap()
            ],
            'tickets_needed' => $ticketcap,
            'tickets_starting' => $startingTickets,
            'round_duration' => $startingTimer,
            'times' => [
                'round' => [
                    'humanize' => MainHelper::secToStr($round, TRUE),
                    'seconds' => (int) $round
                ],
                'uptime' => [
                    'humanize' => MainHelper::secToStr($uptime, TRUE),
                    'seconds' => (int) $uptime
                ],
                'remaining' => [
                    'humanize' => $info[2] >= 4 ? MainHelper::secToStr($startingTimer - $round, TRUE) : 'PreMatch',
                    'seconds' => $startingTimer - $round
                ]
            ]
        ];

        return $this;
    }

    /**
     * Gets the next map in the rotation
     * @return array
     */
    private function getNextMap()
    {
        $index = $this->client->adminMaplistGetNextMapIndex();
        $maps = $this->getMapList();

        foreach($maps as $map)
        {
            if($map['index'] == $index)
                return $map;
        }
    }

    /**
     * Generates the servers maplist into a useable array
     * @return array
     */
    private function getMapList()
    {
        $maps = $this->client->adminMaplistList();

        $list = [];

        for($i = 0; $i < $maps[1]; $i++)
        {
            $map = $maps[ ($maps[2]) * $i + $maps[2] ];
            $mode = $maps[ ($maps[2]) * $i + $maps[2] + 1];
            $rounds = $maps[ ($maps[2]) * $i + $maps[2] + 2];

            $list[] = [
                'map' => [
                    'name' => head($this->client->getMapName( $map )),
                    'uri' => $map
                ],
                'mode' => [
                    'name' => head($this->client->getPlaymodeName( $mode )),
                    'uri' => $mode
                ],
                'rounds' => (int) $rounds,
                'index' => $i
            ];
        }

        return $list;
    }

    /**
     * Simply returns whats in $this->data array
     * @return array
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * Correctly sets the factions name
     * @return this
     */
    private function setFactions()
    {
        if( ! $this->check() )
            throw new \Exception("Setting team factions requires RCON login.");

        $teamFactions = $this->client->adminVarGetTeamFaction(NULL);

        if(in_array($this->client->getCurrentPlaymode(), ['SquadDeathMatch0']))
        {
            $this->TEAM1 = 'Alpha';
            $this->TEAM2 = 'Bravo';
            $this->TEAM3 = 'Charlie';
            $this->TEAM4 = 'Delta';
        }
        else
        {
            $this->TEAM0 = $teamFactions[0][0];
            $this->TEAM1 = $teamFactions[0][ $teamFactions[1][1] + 1];
            $this->TEAM2 = $teamFactions[0][ $teamFactions[1][2] + 1];
            $this->TEAM3 = $teamFactions[0][ $teamFactions[1][3] + 1];
            $this->TEAM4 = $teamFactions[0][ $teamFactions[1][4] + 1];
        }

        return $this;
    }

    /**
     * Gets the players database information
     * @return void
     */
    private function getPlayerDBData()
    {
        // If the server is empty do not continue
        if( $this->data['server']['players']['online'] == 0 )
            return FALSE;

        $players = [];

        foreach($this->data['teams'] as $teamID => $team)
        {
            if( array_key_exists('players', $team) )
            {
                foreach($team['players'] as $player)
                {
                    $players[] = $player['guid'];
                }
            }

            if( array_key_exists('spectators', $team) )
            {
                foreach($team['spectators'] as $player)
                {
                    $players[] = $player['guid'];
                }
            }
        }

        // If players array is empty do not continue
        if( empty($players) ) return FALSE;

        $playersDB = Player::where('GameID', $this->gameID)->whereIn('EAGUID', $players)->get();

        $this->playerDBLoop($players, $playersDB);
        $this->playerDBLoop($players, $playersDB, 'spectators');
        $this->playerDBLoop($players, $playersDB, 'commander');
        $this->getOnlineAdmins();

        return $this;
    }

    /**
     * Checks the player list for admins currently in-game.
     *
     * @return void
     */
    private function getOnlineAdmins()
    {
        $adminlist = DB::select(
            File::get( storage_path() . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'adminList.sql' ),
            [$this->gameID]
        );

        foreach(['players', 'spectators', 'commander'] as $type)
        {
            foreach($this->data['teams'] as $teamID => $team)
            {
                if( array_key_exists($type, $team) )
                {
                    foreach($team[$type] as $index => $player)
                    {
                        foreach($adminlist as $index2 => $player2)
                        {
                            if($player['guid'] == $player2->EAGUID)
                            {
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
     * Function to handle assigning of the DB player object to the playerlist
     * Only used by the getPlayerDBData() function
     *
     * @param  array  $players
     * @param  object $dbPlayers
     * @param  string $type      Valid types are players, spectators, and commander
     * @return void
     */
    private function playerDBLoop($players, $dbPlayers, $type = 'players')
    {
        foreach($this->data['teams'] as $teamID => $team)
        {
            if( array_key_exists($type, $team) )
            {
                foreach($team[$type] as $index => $player)
                {
                    foreach($dbPlayers as $index2 => $player2)
                    {
                        if($player['guid'] == $player2->EAGUID)
                        {
                            $this->data['teams'][$teamID][$type][$index]['_player'] = $player2;
                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }
}
