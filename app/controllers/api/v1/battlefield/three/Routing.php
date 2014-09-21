<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield3;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class Routing extends \BaseController
{
    const GAME = 'BF3';

    protected $_gameId = 0;

    public function __construct()
    {
        // Fetch the games database id and assign it
        $this->_gameId = Helper::getGameId(self::GAME);
    }

    public function missingMethod($paramters = array())
    {
        return Helper::response('error', 'Requested method not found', array(), 400);
    }

    public function getPopulation()
    {
        $servers = Server::where('GameID', $this->_gameId)->where('ConnectionState', 'on')->get();

        if(!$servers)
            return Helper::response('error', 'No servers could be found or are not enabled');

        $mapNamesXML = app_path() . "/thirdparty/bf3/mapNames.xml";

        $totalUsed = 0;
        $totalMax  = 0;

        foreach($servers as $server)
        {
            $data['servers'][] = array(
                'id'                => $server->ServerID,
                'full_server_name'  => $server->ServerName,
                'short_server_name' => null,
                'max'               => $server->maxSlots,
                'used'              => $server->usedSlots,
                'map'               => head(BFHelper::getMapName($server->mapName, $mapNamesXML)),
                'percentage'        => Helper::calculPercentage($server->usedSlots, $server->maxSlots, 1)
            );

            $totalUsed += $server->usedSlots;
            $totalMax += $server->maxSlots;
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

    public function getLatestBans()
    {
        $query = Ban::select('ban_id', 'player_id', 'target_name', 'source_name', 'source_id', 'ban_status', 'ban_startTime', 'ban_endTime', 'record_message')
                ->join('adkats_records_main', 'adkats_bans.latest_record_id', '=', 'adkats_records_main.record_id')
                ->whereIn('ban_status', array('Active', 'Disabled'))
                ->orderBy('ban_startTime', 'desc');

        if(Input::has('limit'))
        {
            $validate = Validator::make(Input::all(), array(
                'limit' => 'required|min:1|numeric'
            ));

            if($validate->fails())
            {
                $message = $validate->messages();
                return Helper::response('error', $message->first('limit'));
            }

            if(Input::get('limit') >= 200)
            {
                return Helper::response('success', 'Results found and are paginated', $query->paginate(60)->toArray());
            }

            return Helper::response('success', 'Results found and are paginated', $query->paginate(Input::get('limit'))->toArray());
        }

        return Helper::response('success', 'Results found', $query->take(30)->get()->toArray());
    }
}
