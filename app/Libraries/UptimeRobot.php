<?php

namespace BFACP\Libraries;

use BFACP\Battlefield\Server\Server;
use BFACP\Exceptions\UptimeRobotException;
use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;

/**
 * Class UptimeRobot
 */
class UptimeRobot
{
    /**
     * @var string
     */
    public static $apiurl = 'https://api.uptimerobot.com';

    /**
     * Account specific
     *
     * @var string
     */
    private $apikey;

    /**
     * @var array
     */
    protected static $types = [
        1 => 'HTTP(S)',
        2 => 'Keyword',
        3 => 'Ping',
        4 => 'Port',
    ];

    /**
     * @var array
     */
    protected static $subTypes = [
        1  => 'HTTP',
        2  => 'HTTPS',
        3  => 'FTP',
        4  => 'SMTP',
        5  => 'POP3',
        6  => 'IMAP',
        99 => 'Custom Port',
    ];

    /**
     * @var array
     */
    protected static $status = [
        0 => 'paused',
        1 => 'not checked yet',
        2 => 'up',
        8 => 'seems down',
        9 => 'down',
    ];

    /**
     * @var array
     */
    protected static $logTypes = [
        1  => 'down',
        2  => 'up',
        99 => 'paused',
        98 => 'started',
    ];

    /**
     * @var Client
     */
    private $guzzle;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $options = [];

    /**
     * Total monitors
     *
     * @var int
     */
    protected $total = 0;

    /**
     * UptimeRobot timezone
     *
     * @var string|null
     */
    protected $timezone = null;

    /**
     * UptimeRobot constructor.
     *
     * @param Client $guzzle
     * @param Config $config
     */
    public function __construct(Client $guzzle, Config $config)
    {
        $this->guzzle = $guzzle;
        $this->config = $config;
        $this->boot();
    }

    /**
     * @throws UptimeRobotException
     */
    private function boot()
    {
        $this->setApikey($this->config->get('bfacp.uptimerobot.key'));

        if (empty($this->getApikey())) {
            throw new UptimeRobotException('API key not set.');
        }

        $this->options['apiKey'] = $this->getApikey();
        $this->options['noJsonCallback'] = true;
        $this->options['format'] = 'json';
    }

    /**
     * @param Server $server
     *
     * @return int
     * @throws UptimeRobotException
     */
    public function createMonitor(Server $server)
    {
        $opts = [
            'monitorFriendlyName' => trim($server->ServerName),
            'monitorURL'          => $server->ip,
            'monitorPort'         => $server->port,
            'monitorType'         => 4,
            'monitorSubType'      => 99,
        ];

        $request = $this->send('newMonitor', $opts, 'POST');

        $id = (int) $request['monitor']['id'];

        $server->setting()->update(['monitor_key' => $id]);

        return $id;
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Support\Collection
     * @throws UptimeRobotException
     */
    public function editMonitor(Server $server)
    {
        $opts = [
            'monitorID'           => $server->setting->monitor_key,
            'monitorFriendlyName' => trim($server->ServerName),
        ];

        $request = $this->send('editMonitor', $opts, 'POST');

        return $request;
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Support\Collection
     * @throws UptimeRobotException
     */
    public function deleteMonitor(Server $server)
    {
        $opts = [
            'monitorID' => $server->setting->monitor_key,
        ];

        $request = $this->send('deleteMonitor', $opts, 'POST');

        return $request;
    }

    /**
     * @param Server $server
     *
     * @return \Illuminate\Support\Collection
     * @throws UptimeRobotException
     */
    public function resetMonitor(Server $server)
    {
        $opts = [
            'monitorID' => $server->setting->monitor_key,
        ];

        $request = $this->send('resetMonitor', $opts, 'POST');

        return $request;
    }

    /**
     * Returns all monitors or only the ones in $monitorIds
     *
     * @param array $monitorIds
     *
     * @return \Illuminate\Support\Collection
     * @throws UptimeRobotException
     */
    public function get(array $monitorIds = [])
    {
        $opts = [
            'logs'          => true,
            'showTimezone'  => true,
            'responseTimes' => true,
        ];

        if (! empty($monitorIds)) {
            $opts['monitors'] = implode('-', $monitorIds);
        }

        $results = $this->send('getMonitors', $opts);

        $this->total = (int) $results['total'];

        $this->timezone = $results['timezone'];

        $monitors = collect();

        $results->each(function ($result) use (&$monitors) {
            if (is_array($result)) {
                foreach ($result['monitor'] as $monitor) {
                    foreach ($monitor as $k => $v) {
                        if (is_numeric($v) && $k != 'alltimeuptimeratio') {
                            $monitor[$k] = (int) $v;
                        }

                        switch ($k) {
                            case "alltimeuptimeratio":
                                $monitor[$k] = (float) $v;
                                break;
                            case "status":
                                $monitor['status'] = self::$status[$monitor[$k]];
                                break;
                            case "friendlyname":
                                $monitor[$k] = html_entity_decode($monitor[$k]);
                                break;
                            case "type":
                                $monitor[$k] = self::$types[$monitor[$k]];
                                break;
                            case "subtype":
                                if ($monitor['type'] == 'Port') {
                                    $monitor[$k] = self::$subTypes[$monitor[$k]];
                                }
                                break;
                            case "log":
                                for ($i = 0; $i < count($v); $i++) {
                                    $type = $monitor[$k][$i]['type'];
                                    $monitor[$k][$i]['type'] = self::$logTypes[$type];
                                }
                                break;
                            case "responsetime":
                                for ($i = 0; $i < count($v); $i++) {
                                    $monitor[$k][$i]['value'] = (int) $monitor[$k][$i]['value'];
                                }
                                break;
                        }
                    }

                    $monitors->push($monitor);
                }
            }
        });

        return [
            'total'    => $this->total,
            'timezone' => $this->timezone,
            'monitors' => $monitors,
        ];
    }

    /**
     * @param string $uri
     * @param array  $opts
     * @param string $method
     *
     * @return \Illuminate\Support\Collection
     * @throws UptimeRobotException
     */
    private function send($uri = '', array $opts = [], $method = 'GET')
    {
        if (empty($uri)) {
            throw new UptimeRobotException('Missing URI');
        }

        try {
            $url = self::$apiurl.'/'.$uri;
            $request = $this->guzzle->request($method, $url, [
                'query' => array_merge($this->options, $opts),
            ]);

            $response = collect(json_decode($request->getBody(), true));

            if ($response['stat'] == 'fail') {
                throw new UptimeRobotException($response['message'], $response['id']);
            }

            return $response;
        } catch (\Exception $e) {
            throw new UptimeRobotException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    private function getApikey()
    {
        return $this->apikey;
    }

    /**
     * @param string $apikey
     */
    private function setApikey($apikey)
    {
        $this->apikey = $apikey;
    }
}
