<?php namespace ADKGamers\Webadmin\Controllers;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Chatlog;
use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use WebadminException, Exception;
use Zizaco\Confide\Facade AS Confide;

class ChatlogController extends \BaseController
{
    protected $layout = 'layout.main';

    public function showChatSearch()
    {
        View::share('title', 'Chatlog Search');

        // Fetch all the servers and put them into an array
        // for the form selct option
        foreach(Server::where('ConnectionState', 'on')->get() as $server)
        {
            $serverlist[$server->ServerID] = '[' . $server->ServerID . '] ' . $server->ServerName;
        }

        // Pass the list to the view
        View::share('serverlist', $serverlist);

        if(Input::has('serverid'))
        {
            if(Auth::check())
            {
                $tz = Confide::user()->preferences->timezone;
            }
            else $tz = 'UTC';

            $this->layout->content = View::make('public.battlefield.chatlog', $this->fetchData())->with('_tz', $tz);
        }
        else
        {
            $this->layout->content = View::make('public.battlefield.chatlog')->with('startDateString', '');
        }
    }

    private function fetchData()
    {
        // Create the array used for pagination links
        $queryString['serverid'] = Input::get('serverid');

        // Get the selected server information
        $server = Server::find(Input::get('serverid'));

        // Begin building the query
        $chatlog = Chatlog::where('ServerID', Input::get('serverid'));

        // Do we have a list of player(s)?
        if(Input::has('players'))
        {
            // Add the list of players to the pagination link array
            $queryString['players'] = Input::get('players');

            // Convert the comma seprated names into an array
            $players = explode(',', Input::get('players'));

            // Create the array to store player ids
            $playerIds = [];

            // Loop through the names
            foreach($players as $player)
            {
                try
                {
                    // Query the database for their player id
                    $pSearch = Player::where('SoldierName', 'LIKE', trim($player) . '%')->where('GameID', $server->GameID)->get();

                    // Check if we have more than one result
                    if($pSearch->count() > 1)
                    {
                        // Loop through the list of players and add them to the player ids array
                        foreach($pSearch as $p)
                        {
                            $playerIds[] = $p->PlayerID;
                        }
                    }
                    else
                    {
                        // Add the player to the player ids array
                        $playerIds[] = $pSearch[0]->PlayerID;
                    }
                }
                catch(Exception $e) {}
            }

            if(count($playerIds) > 0)
            {
                $chatlog->whereIn('logPlayerID', $playerIds);
            }
            else
            {
                $chatlog->whereIn('logSoldierName', $players);
            }
        }

        if(Input::has('daterange'))
        {
            $queryString['daterange'] = Input::get('daterange');

            $date = explode('-', Input::get('daterange'));

            $input_startdatetime = date( 'Y-m-d H:i:s', strtotime( trim( $date[0] ) ) );
            $input_enddatetime   = date( 'Y-m-d H:i:s', strtotime( trim( $date[1] ) ) );

            if(Auth::check())
            {
                $startDate = Helper::LocalToUTC($input_startdatetime);
                $endDate   = Helper::LocalToUTC($input_enddatetime);
            }
            else
            {
                $startDate = $input_startdatetime;
                $endDate   = $input_enddatetime;
            }

            $chatlog->where( 'logDate', '>=', $startDate )
                    ->where( 'logDate', '<=', $endDate );
        }
        else if(!Input::has('daterange') && !Input::get('players') && !Input::get('keywords'))
        {
            $startDate = Carbon::parse('-1 month');
            $chatlog->where( 'logDate', '>=', $startDate );
        }
        else if(!Input::has('daterange') && !Input::get('players') && Input::get('keywords'))
        {
            $startDate = Carbon::parse('-3 months');
            $chatlog->where( 'logDate', '>=', $startDate );
        }

        if(Input::has('keywords'))
        {
            $queryString['keywords'] = Input::get('keywords');

            $chatlog->where(function($query)
            {
                foreach(explode(',', Input::get('keywords')) as $keyword)
                {
                    $query->orWhere('logMessage', 'LIKE', '% ' . trim($keyword) . '%');
                }
            });
        }

        if(Input::has('excludeCommoRose'))
        {
            $queryString['excludeCommoRose'] = Input::get('excludeCommoRose');

            $comoRoseCode = array(
                'ID_CHAT_REQUEST_MEDIC',
                'ID_CHAT_REQUEST_AMMO',
                'ID_CHAT_THANKS',
                'ID_CHAT_REQUEST_RIDE',
                'ID_CHAT_AFFIRMATIVE',
                'ID_CHAT_GOGOGO',
                'ID_CHAT_SORRY',
                'ID_CHAT_ATTACK/DEFEND',
                'ID_CHAT_REQUEST_ORDER',
                'ID_CHAT_GET_IN',
                'ID_CHAT_NEGATIVE',
                'ID_CHAT_GET_OUT',
                'ID_CHAT_REQUEST_REPAIRS'
            );

            $chatlog->whereNotIn('logMessage', $comoRoseCode);
        }

        if(Input::has('excludeServer') && Input::get('excludeServer') == 1)
        {
            $queryString['excludeServer'] = 1;
            $chatlog->whereNotNull('logPlayerID');
        }

        $results = $chatlog->orderBy('logDate', 'desc')->simplePaginate(30);

        if(isset($startDate))
        {
            $startDateString = date('m/d/Y h:i A', strtotime($startDate)) . ' - ' . date('m/d/Y h:i A', time());
        }
        else $startDateString = FALSE;

        return array(
            'appendString' => $queryString,
            'results' => $results,
            'startDateString' => $startDateString
        );
    }
}
