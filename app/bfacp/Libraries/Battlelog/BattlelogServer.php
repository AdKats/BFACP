<?php namespace BFACP\Libraries\Battlelog;

use BFACP\Battlefield\Server;
use Exception;

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

            $uri = sprintf($this->uris['generic']['servers']['players_online'], $game, $this->server->setting->battlelog_server_guid)

            $response = $this->sendRequest($uri);

            return (int) $response['slots'][1]['current'];
        } catch(Exception $e) {
            return -1;
        }
    }
}
