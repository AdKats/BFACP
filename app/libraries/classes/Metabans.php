<?php namespace ADKGamers\Webadmin\Libs;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use Illuminate\Support\Facades\Config;
use Requests;

class Metabans
{
    const API_SERVER = "http://www.metabans.com/api";

    private $api_key_hash = '';

    private $api_key      = '';

    private $api_user     = '';

    private $api_salt     = '';

    private $auth         = array();

    public $base62;

    public function __construct()
    {
        $this->api_salt     = mt_rand(10000, 99999);
        $this->api_key      = Config::get('webadmin.MB-KEY', NULL);
        $this->api_user     = Config::get('webadmin.MB-USR', NULL);
        $this->api_key_hash = sha1($this->api_salt . $this->api_key);

        $this->auth = array(
            'username' => $this->api_user,
            'apikey'   => $this->api_key_hash,
            'salt'     => $this->api_salt
        );

        $this->base62 = new Base62;
    }

    private function makeRequest($data)
    {
        $response = Requests::post(self::API_SERVER, array(), $data);

        if($response->success)
        {
            $response_data = json_decode($response->body, true);
            return $response_data['responses'][0]['data'];
        }

        return $response->status_code;
    }

    public function feed()
    {
        if(empty($this->api_key) || empty($this->api_user))
            return Helper::response('error', 'Metabans configuration not set up');

        $results = self::request(array('mbo_assessments' => array('account_name' => Config::get('webadmin.MB-ACC'))));

        if(is_array($results))
        {
            foreach($results['assessments'] as $key => $feed)
            {
                $tmp[] = array(
                    'id'             => (int) $feed['assessment_id'],
                    'player'         => $feed['player_name'],
                    'reason'         => $feed['reason'],
                    'action'         => $feed['action_type'],
                    'timestamp'      => Helper::UTCToLocal($feed['stamp'])->toDayDateTimeString(),
                    'stamp'          => $feed['stamp'],
                    'game_name'      => str_replace('_', NULL, $feed['game_name']),
                    'full_game_name' => $feed['full_game_name'],
                    'playercard_url' => 'http://metabans.com/player?i=' . $this->base62->encode($feed['player_id']),
                    'assessment_url' => 'http://metabans.com/assessment?i=' . $this->base62->encode($feed['assessment_id'])
                );
            }
        }
        else return Helper::response('error', 'Request to metabans api failed', $results);

        return Helper::response('success', NULL, $tmp);
    }

    public function request($requests)
    {
        $parsed = array();
        $needs_auth = false;

        foreach($requests as $action => $param)
        {
            if(substr($action, 0, 3) == "mb_")
            {
                $needs_auth = true;
            }

            $parsed[] = array_merge(array("action" => (string)$action), (array)$param);
        }

        $request = array("requests" => $parsed);

        if($needs_auth)
        {
            $request = array_merge($request, $this->get_auth());
        }

        return $this->makeRequest($request);
    }

    private function get_auth()
    {
        return array("username" => $this->api_user, "apikey" => $this->api_key_hash, "salt" => $this->api_salt);
    }
}
