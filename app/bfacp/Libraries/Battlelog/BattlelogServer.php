<?php namespace BFACP\Libraries\Battlelog;


use Exception;

class BattlelogServer extends BattlelogAPI
{
    /**
     * Battlelog Search Options
     *
     * @var array
     */
    private $options = [
        'filtered'       => 1,
        'expand'         => 1,
        'settings'       => '',
        'useLocation'    => 1,
        'useAdvanced'    => 1,
        'gameexpansions' => -1,
        'q'              => '',
        'mapRotation'    => -1,
        'modeRotation'   => -1,
        'password'       => -1,
        'osls'           => -1,
        'vvsa'           => -1,
        'vffi'           => -1,
        'vaba'           => -1,
        'vkca'           => -1,
        'v3ca'           => -1,
        'v3sp'           => -1,
        'vmsp'           => -1,
        'vrhe'           => -1,
        'vhud'           => -1,
        'vmin'           => -1,
        'vnta'           => -1,
        'vbdm-min'       => 1,
        'vbdm-max'       => 300,
        'vprt-min'       => 1,
        'vprt-max'       => 300,
        'vshe-min'       => 1,
        'vshe-max'       => 300,
        'vtkk-min'       => 1,
        'vttk-max'       => 99,
        'vnit-min'       => 30,
        'vnit-max'       => 86400,
        'vtkc-min'       => 1,
        'vtkc-max'       => 99,
        'vvsd-min'       => 0,
        'vvsd-max'       => 500,
        'vgmc-min'       => 0,
        'vgmc-max'       => 500,
    ];

    /**
     * Returns the number of players currently in queue
     *
     * @return int
     */
    public function inQueue()
    {
        try {
            switch ($this->server->game->Name) {
                case 'BFHL':
                    $game = 'bfh';
                    break;

                default:
                    $game = strtolower($this->server->game->Name);
            }

            $uri = sprintf($this->uris['generic']['servers']['players_online'], $game,
                $this->server->setting->battlelog_guid);

            $response = $this->sendRequest($uri);

            return (int)$response['slots'][1]['current'];
        } catch (Exception $e) {
            return -1;
        }
    }

    /**
     * Returns the server GUID
     *
     * @return string
     */
    public function guid()
    {
        $servers = $this->search();

        if (!is_null($servers) && !empty($servers)) {
            if (count($servers) > 1) {
                foreach ($servers as $server) {
                    if ($server['name'] == $this->server->ServerName) {
                        return $server['guid'];
                    }
                }
            } else {
                return $servers[0]['guid'];
            }
        }

        return;
    }

    /**
     * Search for server on battlelog
     *
     * @return array
     */
    public function search()
    {
        switch ($this->server->game->Name) {
            case 'BFHL':
                $game = 'bfh';
                break;

            default:
                $game = strtolower($this->server->game->Name);
        }

        $this->options['q'] = $this->server->ServerName;

        $query = http_build_query($this->options);

        $uri = sprintf($this->uris['generic']['servers']['server_browser'], $game, $query);

        $response = $this->sendRequest($uri);

        if (array_key_exists('servers', $response['globalContext'])) {
            return $response['globalContext']['servers'];
        }

        return;
    }
}
