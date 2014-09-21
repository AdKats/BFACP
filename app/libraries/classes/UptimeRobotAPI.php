<?php namespace ADKGamers\Webadmin\Libs;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Requests, WebadminException, Exception, Requests_Exception;

class UptimeRobotAPI
{
    const URL = "http://api.uptimerobot.com/";

    public $apikey = '';

    public $monitorTypes = array(
        1 => 'HTTP(S)',
        2 => 'Keyword',
        3 => 'Ping',
        4 => 'Port',
    );

    public $monitorSubTypes = array(
        1  => 'HTTP',
        2  => 'HTTPS',
        3  => 'FTP',
        4  => 'SMTP',
        5  => 'POP3',
        7  => 'IMAP',
        99 => 'Custom Port'
    );

    public $monitorStatus = array(
        0 => 'paused',
        1 => 'not checked yet',
        2 => 'up',
        8 => 'seems down',
        9 => 'down'
    );

    public $logTypes = array(
        1  => 'down',
        2  => 'up',
        99 => 'paused',
        98 => 'started'
    );

    public function __construct()
    {
        $this->apikey = Config::get('webadmin.UPTIMEROBOT-KEY');
    }

    public function getMonitors($id = array())
    {
        if(!empty($id))
            $data['monitors'] = implode('-', $id);

        $data['logs'] = 1;
        $data['responseTimes'] = 1;
        $data['showTimezone'] = 1;

        return $this->make('getMonitors', $data);
    }

    public function editMonitor($id, $sid)
    {
        $s = Server::find($sid);
        $data['monitorID'] = $id;
        $data['monitorFriendlyName'] = $s->ServerName;
        $data['monitorURL'] = Helper::getIpAddr($s->IP_Address);

        return $this->make('editMonitor', $data);
    }

    public function deleteMonitor($id)
    {
        $data['monitorID'] = $id;

        return $this->make('deleteMonitor', $data);
    }

    public function newMonitor(Server $server = NULL)
    {
        if(is_null($server))
            return FALSE;

        $data['monitorFriendlyName'] = $server->ServerName;
        $data['monitorURL'] = Helper::getIpAddr($server->IP_Address);
        $data['monitorPort'] = Helper::getPort($server->IP_Address);
        $data['monitorType'] = 4;
        $data['monitorSubType'] = 99;

        return $this->make('newMonitor', $data);
    }

    public function make($method, $data)
    {
        try
        {
            /**
             * Set the parameters that should always be sent
             */
            $data['apikey'] = $this->apikey;
            $data['noJsonCallback'] = 1;
            $data['format'] = 'json';

            $query = http_build_query($data);

            // Send the request
            $request = Requests::get(self::URL . $method .'?' . $query);

            // Decode the response and return it
            return json_decode($request->body, true);
        }
        catch(Requests_Exception $e)
        {
            throw new WebadminException($e->getMessage());
        }
    }

    public function parse($response)
    {
        if(empty($response))
            return FALSE;

        $temp = array();

        foreach($response['monitors']['monitor'] as $index => $m)
        {
            $temp[$index] = array(
                'id' => intval($m['id']),
                'friendlyName' => html_entity_decode($m['friendlyname']),
                'type' => $this->monitorTypes[$m['type']],
                'status' => $this->monitorStatus[$m['status']],
                'ratio' => floatval($m['alltimeuptimeratio'])
            );

            foreach($m['log'] as $log)
            {
                $temp[$index]['logs'][] = array(
                    'type' => $this->logTypes[$log['type']],
                    'timestamp' => $log['datetime']
                );
            }

            foreach($m['responsetime'] as $ping)
            {
                $temp[$index]['ms'][] = array(
                    'timestamp' => $ping['datetime'],
                    'value' => intval($ping['value'])
                );
            }
        }

        return (count($temp) == 1 ? $temp[0] : $temp);
    }
}
