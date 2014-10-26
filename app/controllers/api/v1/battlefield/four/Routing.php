<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield4;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Battlefield AS BFHelper;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class Routing extends \BaseController
{
    const GAME = 'BF4';

    protected $_gameId = 0;

    public function __construct()
    {
        // Fetch the games database id and assign it
        $this->_gameId = BF4_DB_ID;
    }

    public function missingMethod($paramters = array())
    {
        return Helper::response('error', 'Requested method not found', array(), 400);
    }

    public function getPopulation()
    {
        $servers = Server::with('setting')->where('GameID', $this->_gameId)->orderBy(Config::get('webadmin.SERVERORDER', 'ServerID'))->where('ConnectionState', 'on')->get();

        if(!$servers)
            return Helper::response('error', 'No servers could be found or are not enabled');

        $mapNamesXML = app_path() . "/thirdparty/bf4/mapNames.xml";

        $totalUsed = 0;
        $totalMax  = 0;

        foreach($servers as $server)
        {
            $data['servers'][] = array(
                'id'                => $server->ServerID,
                'full_server_name'  => $server->ServerName,
                'short_server_name' => is_null($server->setting) ? NULL : $server->strip($server->setting->name_strip),
                'max'               => $server->maxSlots,
                'used'              => $server->usedSlots,
                'map'               => head(BFHelper::getMapName($server->mapName, $mapNamesXML)),
                'percentage'        => Helper::calculPercentage($server->usedSlots, $server->maxSlots, 1)
            );

            $totalUsed += $server->usedSlots;
            $totalMax += $server->maxSlots;
        }

        if(Config::get('webadmin.SERVERORDER') == 'ServerName')
        {
            $server_sort = [];

            foreach($data['servers'] as $key => $row)
                $server_sort[$key] = $row['full_server_name'];

            array_multisort($server_sort, SORT_ASC, SORT_STRING, $data['servers']);
        }

        $data['total'] = array(
            'totalUsed'  => (integer) $totalUsed,
            'totalMax'   => (integer) $totalMax,
            'percentage' => Helper::calculPercentage($totalUsed, $totalMax, 1)
        );

        return Helper::response('success', 'Found', $data);
    }

    public function postPopulationGraph()
    {
        $i = 0;

        $servers = Server::where('GameID', $this->_gameId)->where('ConnectionState', 'on')->get();

        $startDate = date('Y-m-d', strtotime("-2 week")) . ' 00:00:00';
        $endDate   = date('Y-m-d', time()) . ' 23:59:59';

        if(!$servers)
            return Helper::response('error', 'Could not fetch any servers');

        foreach($servers as $server)
        {
            $sql = DB::select(file_get_contents(storage_path() . '/sql/populationgraph.sql'), [$server->ServerID, $startDate, $endDate]);

            $graphData['series'][$i]['name'] = $server->ServerName;

            foreach($sql as $row)
            {
                $jsDate = strtotime( date('Y-m-d H', strtotime( $row->TimeRoundEnd ) ) . ':00:00' ) * 1000;

                $graphData['series'][$i]['data'][] = [$jsDate, (int) $row->AvgPlayers];
            }

            $i++;
        }

        return Helper::response('success', 'OK', $graphData);
    }
}
