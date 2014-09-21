<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield\Admin\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\BF3Conn;
use ADKGamers\Webadmin\Libs\BF4Conn;
use ADKGamers\Webadmin\Libs\Helpers\Battlefield AS BFHelper;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\AdKats\Command AS AdKatsCommand;
use ADKGamers\Webadmin\Models\AdKats\Setting AS AdKatsSetting;
use ADKGamers\Webadmin\Models\Battlefield\Ban;
use ADKGamers\Webadmin\Models\Battlefield\Chatlog;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Models\Battlefield\Setting AS GameSetting;
use BattlefieldException, Exception, Confide;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Zizaco\Entrust\EntrustFacade AS Entrust;

class ReportViewerController extends \BaseController
{
    public function getIndex()
    {
        $query = Record::select('record_id', 'command_type', 'command_action', 'target_name', 'target_id', 'source_name', 'source_id', 'record_message', 'record_time', 'ServerName')
                    ->join('tbl_server', 'adkats_records_main.server_id', '=', 'tbl_server.ServerID')
                    ->whereIn('command_type', [18, 20])
                    ->orderBy('record_time', 'desc')->paginate(100);


    }
}
