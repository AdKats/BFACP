<?php namespace BFACP\Libraries\Battlelog;

use BFACP\Battlefield\Server;

class Battlelog
{
    /**
     * Battlelog Base URL
     */
    const BLOG = 'http://battlelog.battlefield.com/';

    /**
     * Guzzle Client
     * @var GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Server object
     * @var BFACP\Battlefield\Server
     */
    protected $server;

    /**
     * URIs for battlelog
     * @var array
     */
    protected $uris = [
        'bf4'     => [
            'overview'      => '%s/warsawoverviewpopulate/%u/1/',
            'weapons'       => '%s/warsawWeaponsPopulateStats/%u/1/stats/',
            'vehicles'      => '%s/warsawvehiclesPopulateStats/%u/1/stats/',
            'battlereports' => '%s/warsawbattlereportspopulate/%u/2048/1/',
            'soldier'       => '%s/soldier/%u/stats/%u/pc/'
        ],
        'bf3'     => [
            'overview' => '%s/overviewPopulateStats/%u/bf3-ru-assault/1/',
            'weapons'  => '%s/weaponsPopulateStats/%u/1/stats/',
            'vehicles' => '%s/vehiclesPopulateStats/%u/1/stats/',
            //'battlereports' => '%s/warsawbattlereportspopulate/%u/2048/1/',
            'soldier'  => '%s/soldier/%u/stats/%u/pc/'
        ],
        'bfh'     => [
            'overview'      => '%s/bfhoverviewpopulate/%u/1/',
            'weapons'       => '%s/BFHWeaponsPopulateStats/%u/1/stats/',
            'vehicles'      => '%s/bfhvehiclesPopulateStats/%u/1/stats/',
            'battlereports' => '%s/warsawbattlereportspopulate/%u/8192/1/',
            'soldier'       => '%s/soldier/%u/stats/%u/pc/'
        ],
        'generic' => [
            'profile' => '%s/user/%s',
            'servers' => [
                'players_online' => '%s/servers/getNumPlayersOnServer/pc/%s',
                'server_browser' => '%s/servers/pc/?%s'
            ]
        ]
    ];

    /**
     * Battlelog Game Codes
     * @var array
     */
    protected $games = [
        'bfh' => 8192,
        'bf4' => 2048,
        'bf3' => 2
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

    /**
     * Sends the request to battlelog
     * @param  string $uri
     * @return array
     */
    protected function sendRequest($uri)
    {
        $request = $this->guzzle->get(static::BLOG . $uri, [
            'headers' => [
                'X-AjaxNavigation' => true
            ]
        ]);

        return $request->json();
    }
}
