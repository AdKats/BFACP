<?php

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Setting;

$list = [];

$format = function(&$list, $keys, $val) use (&$format)
{
    $keys ? $format($list[array_shift($keys)], $keys, $val) : $list = $val;
};

foreach(Setting::all() as $setting)
{
    if(in_array($setting->token, ['BF3', 'BF4']))
    {
        $format($list, explode('.', $setting->token), filter_var($setting->context, FILTER_VALIDATE_BOOLEAN));
    }
    else $format($list, explode('.', $setting->token), $setting->context);
}

return $list;
