<?php namespace ADKGamers\Webadmin\Controllers\Admin\AdKats;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\AdKats\Setting AS AdKatsSetting;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Zizaco\Confide\Facade AS Confide;
use Zizaco\Entrust\EntrustFacade AS Entrust;

class PluginController extends \BaseController
{
    public function index()
    {
        $settings = AdKatsSetting::where('server_id', 1)->get();

        foreach($settings as $key => $setting)
        {
            switch($setting->setting_type)
            {
                case "int":
                    $settings[$key]->setting_value = intval($setting->setting_value);
                break;

                case "bool":
                    $settings[$key]->setting_value = ($setting->setting_value == 'True' ? TRUE : FALSE);
                break;

                case "double":
                    $settings[$key]->setting_value = floatval($setting->setting_value);
                break;
            }
        }

        return [];
    }
}
