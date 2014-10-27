<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield\Admin\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\AdKats\Special;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Zizaco\Confide\Facade AS Confide;
use Zizaco\Entrust\EntrustFacade AS Entrust;

class SpecialPlayersController extends \BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $results = Special::with('player', 'server')->paginate(100)->toArray();

        foreach($results['data'] as $key => $result)
        {
            $results['data'][$key]['player_effective_local']  = Helper::UTCToLocal($result['player_effective'])->toIso8601String();
            $results['data'][$key]['player_expiration_local'] = Helper::UTCToLocal($result['player_expiration'])->toIso8601String();
        }

        return Helper::response('success', NULL, $results);
    }
}
