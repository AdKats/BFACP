<?php namespace BFACP\Repositories\Scoreboard;

use BFACP\Contracts\Scoreboard;
use BFACP\Battlefield\Server;

class DBRepository implements Scoreboard
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
     * Load server by ID
     *
     * @param  integer $id Server DB ID
     * @return this
     */
    public function find($id)
    {
        $server = Server::with('scores')->findOrFail($id);

        $server = $server->load(['scoreboard.player' => function($q) use($server) {
            $q->where('GameID', $server->game->GameID);
        }]);

        $this->gameID        = $server->game->GameID;
        $this->gameName      = $server->game->Name;
        $this->port          = $server->port;
        $this->serverIP      = $server->ip;
        $this->server        = $server;
        $this->connected     = TRUE;
        $this->authenticated = TRUE;

        return $this;
    }

    /**
     * Determine if we are connected and authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return $this->connected && $this->authenticated;
    }

    /**
     * Attempt to establish connection and login.
     *
     * @param  string  $ip   Server IPv4 Address
     * @param  integer $port Server RCON Port
     * @param  string  $pass Server RCON Password
     * @return bool
     */
    public function attempt($ip, $port, $pass)
    {
        //
    }
}
