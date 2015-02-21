<?php namespace BFACP\Libraries\Battlelog;

use BFACP\Battlefield\Server;
use Exception;
use GuzzleHttp\Client AS Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class BattlelogServer extends Battlelog
{
    /**
     * Returns the number of players currently in queue
     * @return int
     */
    public function inQueue()
    {
        try {
            $game = strtolower($this->server->game->Name);

            $uri = sprintf("%s/servers/%s/pc/%s", $game, $this->uris['bf4']['players_online'], $this->server->setting->battlelog_server_guid);

            $response = $this->guzzle->get(static::BLOG . $uri);

            $response = $response->json();

            return (int) $response['slots'][1]['current'];
        } catch(Exception $e) {
            return -1;
        }
    }
}
