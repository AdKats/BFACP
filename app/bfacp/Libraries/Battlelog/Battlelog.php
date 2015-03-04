<?php namespace BFACP\Libraries\Battlelog;

use GuzzleHttp\Client AS Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use BFACP\Battlefield\Server;

class Battlelog
{
    /**
     * Battlelog Base URL
     */
    const BLOG = "http://battlelog.battlefield.com/";

    /**
     * Guzzle Client
     * @var GuzzleHttp\Client
     */
    protected $guzzle;

    protected $server;

    protected $uris = [
        'bf4' => [
            'players_online' => 'getNumPlayersOnServer'
        ]
    ];

    public function __construct()
    {
        $this->guzzle = \App::make('GuzzleHttp\Client');
    }

    /**
     * Set the server
     * @param  Server $server
     * @return $this
     */
    public function server(Server $server)
    {
        $this->server = $server;

        return $this;
    }
}
