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

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('bfadmincp_settings')->delete();

        Setting::insert([
            ['token' => 'VERSION', 'context' => '1.5.0', 'description' => 'Current version'],
            ['token' => 'BF3', 'context' => true, 'description' => 'Enables or Disables the Battlefield 3 sections'],
            ['token' => 'BF4', 'context' => true, 'description' => 'Enables or Disables the Battlefield 4 sections'],
            ['token' => 'SERVERORDER', 'context' => 'ServerID', 'description' => 'Order your servers by the database ID or by the name of the server'],
            ['token' => 'MB-KEY', 'context' => '', 'description' => 'Metabans API Key'],
            ['token' => 'MB-USR', 'context' => '', 'description' => 'Metabans API User'],
            ['token' => 'MB-ACC', 'context' => '', 'description' => 'Metabans API Account'],
            ['token' => 'GAMEMEAPIURL', 'context' => '', 'description' => 'gameME API URL - Example: http://stats.adkgamers.com/api'],
            ['token' => 'MOTD', 'context' => true, 'description' => 'Enable or Disable the message of the day'],
            ['token' => 'MOTD-TXT', 'context' => '', 'description' => 'Set the message of the day'],
            ['token' => 'ONLYAUTHUSERS', 'context' => false, 'description' => 'If checked only users with accounts will be able to view the BFAdminCP'],
            ['token' => 'UPTIMEROBOT-KEY', 'context' => '', 'description' => 'Main API Key'],
            ['token' => 'UPTIMEROBOT', 'context' => false, 'description' => 'Use Uptime Robot'],
            ['token' => 'FORCESSL', 'context' => false, 'description' => 'Force use of SSL'],
        ]);
    }
}
