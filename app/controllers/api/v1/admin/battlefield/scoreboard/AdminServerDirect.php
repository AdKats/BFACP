<?php namespace ADKGamers\Webadmin\Controllers\Api\v1\Battlefield\Admin;

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

class AdminServerDirect extends \BaseController
{
    /**
     * Game identifier from database
     * @var integer
     */
    private $_gameid = NULL;

    /**
     * Game abraviation
     * @var string
     */
    private $game = '';

    /**
     * Server identifier in database
     * @var integer
     */
    private $server_id = NULL;

    /**
     * List of pre-defined messages from AdKats
     * @var array
     */
    private $presetMsgs = array();

    /**
     * Max temp ban duration in minutes
     * @var integer
     */
    private $maxTbanDuration = 60;

    /**
     * Server IP Address
     * @var string
     */
    private $server_ip = '0.0.0.0';

    /**
     * Server RCON Port
     * @var integer
     */
    private $server_port = 47200;

    /**
     * Variable to hold the rendered data
     * @var array
     */
    private $data = array();

    /**
     * Variable to assign to game connection class
     * @var object
     */
    private $conn = NULL;

    /**
     * User object
     * @var object
     */
    private $user = NULL;

    /**
     * Admin name
     * @var string
     */
    private $admin = NULL;

    /**
     * Server object
     * @var object
     */
    private $server = NULL;

    /**
     * Variable to hold the players information
     * @var object
     */
    public $players = array();

    /**
     * Response to send back
     * @var array
     */
    public $finalized = array();

    public function __construct()
    {
        try
        {
            $server = Server::with('setting')->find(Input::get('server_id'));

            // Did we get a result?
            if(!$server || !is_numeric(Input::get('server_id')))
                throw new BattlefieldException("Could not load the requested server");

            $this->_gameid   = $server->GameID;
            $this->server_id = $server->ServerID;
            $this->game      = $server->gameIdent();

            $this->user = Confide::user();

            // Parse out the Hostname/IP without port number
            $this->server_ip = Helper::getIpAddr($server->IP_Address);

            // Get the port number from the Hostname/IP
            $this->server_port = Helper::getPort($server->IP_Address);

            if(!Validator::make(array('ip' => $this->server_ip), array('ip' => 'IP'))->passes())
                throw new BattlefieldException("Invalid IP Address: " . $this->server_ip);

            switch($this->game)
            {
                case "BF3":
                    if( !Entrust::can('scoreboard.bf3') )
                        throw new BattlefieldException('You do not have permission to administrate this server through the web interface.');

                    $this->conn = new BF3Conn(array($this->server_ip, $this->server_port, null));
                    $this->admin = $this->user->preferences->bf3player;
                break;

                case "BF4":
                    if( !Entrust::can('scoreboard.bf4') )
                        throw new BattlefieldException('You do not have permission to administrate this server through the web interface.');

                    $this->conn = new BF4Conn(array($this->server_ip, $this->server_port, null));
                    $this->admin = $this->user->preferences->bf4player;
                break;
            }

            // Check if we are connected to the gameserver
            if(!$this->conn->isConnected())
                throw new BattlefieldException("Could not establish connection to gameserver");

            $gsetting = $server->setting;

            // Attempt to login to the gameserver
            $this->conn->loginSecure($gsetting->getPass());

            if(!$this->conn->isLoggedIn())
                throw new BattlefieldException("Incorrect RCON password");

            foreach ($server->adkatsConfig as $k => $v)
            {
                if($v->setting_name == "Pre-Message List")
                {
                    $this->presetMsgs = explode( '|', urldecode( rawurldecode( $v->setting_value ) ) );
                }
                else if($v->setting_name == "Maximum Temp-Ban Duration Minutes")
                {
                    $this->maxTbanDuration = $v->setting_value;
                }
            }

            $this->players = array_map( 'trim', explode( ',' , Input::get('players') ) );
        }
        catch(BattlefieldException $e)
        {
            $this->finalized = Helper::response('error', $e->getMessage());
        }
        catch(Exception $e)
        {
            $this->finalized = Helper::response('error', $e->getMessage());
        }
    }

    private function _log($command_id, $target, $message = 'No Message', $bypass = 'Y', $duration = 0)
    {
        $datetime = Carbon::now();

        if(!is_null($target))
        {
            $player = Player::where('GameID', $this->_gameid)->where('SoldierName', $target)->first();
        }

        if($command_id == 21)
        {
            $chat                 = new Chatlog;
            $chat->ServerID       = $this->server_id;
            $chat->logDate        = $datetime;
            $chat->logMessage     = $message;
            $chat->logPlayerID    = $this->admin->PlayerID;
            $chat->logSoldierName = $this->admin->SoldierName;
            $chat->logSubset      = 'Global';
            $chat->save();
        }

        $r                  = new Record;
        $r->adkats_read     = $bypass;
        $r->adkats_web      = TRUE;
        $r->command_action  = $command_id;
        $r->command_numeric = $duration;
        $r->command_type    = $command_id;
        $r->record_message  = $message;
        $r->record_time     = $datetime;
        $r->server_id       = $this->server_id;
        $r->source_id       = (empty($this->admin) ? NULL : $this->admin->PlayerID);
        $r->source_name     = (empty($this->admin) ? $this->user->username : $this->admin->SoldierName);
        $r->target_id       = (is_null($target) ? NULL : $player->PlayerID);
        $r->target_name     = (is_null($target) ? 'Server' : $player->SoldierName);
        $r->save();

        return $r;
    }

    private function _buildPlayerListing()
    {
        $players = $this->conn->adminGetPlayerlist();

        $temp = [];

        for($i=0; $i <= $this->conn->getCurrentPlayers(); $i++)
        {
            try
            {
                $player_soldier_name = $players[ ( $players[1] ) * $i + $players[1] + 3 ];

                $temp[] = $player_soldier_name;
            }
            catch(Exception $e) { continue; }
        }

        return $temp;
    }

    private function _createBanRecord(Record $record, Player $player, $bantype, $duration = 30)
    {
        $ban = Ban::where('player_id', $player->PlayerID)->first();

        if($bantype == 7)
        {
            $ban_endTime = Carbon::now()->addMinutes($duration);
        }
        else if($bantype == 8)
        {
            $ban_endTime = Carbon::now()->addYears(20);
        }

        if(!$ban)
        {
            $newBan = new Ban;
            $newBan->player_id = $player->PlayerID;
            $newBan->latest_record_id = $record->record_id;
            $newBan->ban_startTime = Carbon::now();
            $newBan->ban_status = 'Active';

            if($bantype == 7)
            {
                $newBan->ban_endTime = $ban_endTime;
            }
            else if($bantype == 8)
            {
                $newBan->ban_endTime = $ban_endTime;
            }

            $newBan->save();

            $ban_id = $newBan->ban_id;
        }
        else
        {
            $ban->latest_record_id = $record->record_id;
            $ban->ban_startTime = Carbon::now();
            $ban->ban_status = 'Active';

            if($bantype == 7)
            {
                $ban->ban_endTime = $ban_endTime;
            }
            else if($bantype == 8)
            {
                $ban->ban_endTime = $ban_endTime;
            }

            $ban->save();

            $ban_id = $ban->ban_id;
        }

        return Ban::find($ban_id);
    }

    private function _createBanKickMessage(Ban $ban, Record $record)
    {
        // Get the appeal message for ban
        $banAppealMessage    = AdKatsSetting::where('setting_name', 'Additional Ban Message')->where('server_id', $this->server_id)->pluck('setting_value');

        // Should we use the appeal message? True or False
        $banUseAppealMessage = AdKatsSetting::where('setting_name', 'Use Additional Ban Message')->where('server_id', $this->server_id)->pluck('setting_value');

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $ban->ban_startTime);
        $end   = Carbon::createFromFormat('Y-m-d H:i:s', $ban->ban_endTime);

        $years = $start->diffInYears($end);

        if($years > 3)
        {
            $banDurationString = "[perm]";
        }
        else
        {
            $banDurationString = sprintf("[%s]", Helper::timeRemainingDifference($ban->ban_startTime, $ban->ban_endTime));
        }

        $banAppend = $banUseAppealMessage == 'True' ? sprintf("[%s]", $banAppealMessage) : NULL;

        $banSourceName = sprintf("[%s]", $this->admin->SoldierName);

        $finalMessage = $record->record_message . " " . $banDurationString . $banSourceName . $banAppend;

        $cutLength = strlen($finalMessage) - 80;

        if($cutLength > 0)
        {
            $shortenMessage = substr($record->record_message, strlen($record->record_message) - $cutLength);
            $finalMessage = $shortenMessage . " " . $banDurationString . $banSourceName . $banAppend;
        }

        return trim($finalMessage);
    }

    public function postIndex()
    {
        return Helper::response('error', 'Nothing to see here');
    }

    public function postPlayer()
    {
        if( ! Input::has('player_name') )
            return Helper::response('error', 'Missing player name');

        $player = Player::where('GameID', $this->_gameid)->where('SoldierName', Input::get('player_name'))->first();

        if(!$player)
            return Helper::response('error', 'No player found');

        $data = [
            'player_id' => intval($player->PlayerID),
            'player_name' => $player->SoldierName
        ];

        return Helper::response('success', NULL, $data);
    }

    public function postMessage()
    {
        if( ! Input::has('type') && ! Input::has('message') )
            return $this->finalized = Helper::response('error', 'Missing required pramaters');

        $type    = Input::get('type');
        $message = Input::get('message');

        if( ! in_array( $type, ['all', 'team', 'player'] ) )
            return Helper::response('error', 'Invalid type');

        if(starts_with($message, '/'))
            return Helper::response('error', 'Commands through chat are not supported');

        $newMessage = ( !in_array($message, $this->presetMsgs ) ? sprintf("%s: %s", $this->admin->SoldierName, $message) : sprintf("%s", $message) );

        $response = [];

        switch($type)
        {
            case "all":
                if( !Entrust::can('scoreboard.say') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                self::_log(21, NULL, $message);
                $response = $this->conn->adminSayMessageToAll($newMessage);
            break;

            case "team":
                if( !Entrust::can('scoreboard.tsay') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                $response = $this->conn->adminSayMessageToTeam(Input::get('team_id'), $newMessage);
            break;

            case "player":
                if( !Entrust::can('scoreboard.psay') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                foreach($this->players as $player)
                {
                    self::_log(22, $player, $message);
                    $response[] = $this->conn->adminSayMessageToPlayer($player, $newMessage);
                }
            break;
        }

        return Helper::response('success', 'Message Sent!', $response);
    }

    public function postYellMessage()
    {
        if( ! Input::has('type') && ! Input::has('message') )
            return $this->finalized = Helper::response('error', 'Missing required pramaters');

        $type     = Input::get('type');
        $message  = Input::get('message');
        $duration = intval(Input::get('duration', 30));

        if( ! in_array( $type, ['all', 'team', 'player'] ) )
            return Helper::response('error', 'Invalid type');

        $response = [];

        switch($type)
        {
            case "all":
                if( !Entrust::can('scoreboard.yell') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                $log = self::_log(23, NULL, $message);

                $response = $this->conn->adminYellMessage($message, '{%all%}', $duration);
            break;

            case "team":
                if( !Entrust::can('scoreboard.tyell') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                $response = $this->conn->adminYellMessageToTeam($message, Input::get('team_id'), $duration);
            break;

            case "player":
                if( !Entrust::can('scoreboard.pyell') )
                    return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

                foreach($this->players as $player)
                {
                    self::_log(24, $player, $message);
                    $response[] = $this->conn->adminYellMessage($message, $player, $duration);
                }
            break;
        }

        return Helper::response('success', 'Message Sent!', $response);
    }

    public function postKick()
    {
        if( !Entrust::can('scoreboard.kick') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $message = Input::get('message', 'Kicked by admin');

        $player = head( $this->players );

        $serverMessage = sprintf("%s has been kicked from the server. Reason: %s", $player, $message);

        $playerMessage = sprintf("%s [%s]", $message, $this->admin->SoldierName);

        $cutLength = strlen($playerMessage) - 80;

        if($cutLength > 0)
        {
            $shortenMessage = substr($message, strlen($message) - $cutLength);
            $playerMessage  = sprintf("%s [%s]", $shortenMessage, $this->admin->SoldierName);
        }

        $kickedPlayer = $this->conn->adminKickPlayerWithReason( $player, $playerMessage );

        switch( $kickedPlayer )
        {
            case "OK":
                $this->conn->adminSayMessageToAll($serverMessage);
                $log = self::_log(6, $player, $message);
                return Helper::response('success', $serverMessage);
            break;

            case "PlayerNotFound":
                $msg = sprintf("%s was not found on the server.", $player);
                return Helper::response('error', $msg);
            break;

            default:
                return Helper::response('error', "Server returned an unknown error", $kickedPlayer);
        }
    }

    public function postKickAll()
    {
        if( !Entrust::can('scoreboard.kickall') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $message = Input::get('message', 'Kicked by admin');

        $playerMessage = sprintf("%s", $message);

        $kickedPlayers = [];

        $this->players = $this->_buildPlayerListing();

        foreach($this->players as $player)
        {
            $log = self::_log(6, $player, $message);

            $kickedPlayer = $this->conn->adminKickPlayerWithReason($player, $playerMessage);

            $kickedPlayers[] = [
                'player' => $player,
                'server_response' => $kickedPlayer,
                'record' => $log
            ];
        }

        return Helper::response('success', "All players have been kicked from the server.", $kickedPlayers);
    }

    public function postKill()
    {
        if( !Entrust::can('scoreboard.kill') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $message = Input::get('message', 'Slain by an admin');

        $playerMessage = sprintf("You were slain by an admin. Reason: %s", $message);

        $player = head($this->players);

        $killedPlayer = $this->conn->adminKillPlayer( $player );

        switch($killedPlayer)
        {
            case "OK":
                $this->conn->adminSayMessageToPlayer( $player, $message );
                $msg = sprintf("%s was killed. Reason: %s", $player, $message );
                $log = self::_log(3, $player, $message);
                return Helper::response('success', $msg);
            break;

            case "InvalidPlayerName":
                return Helper::response('error', 'Invalid Player Name');
            break;

            case "SoldierNotAlive":
                $msg = sprintf("%s is already dead.", $player);
                return Helper::response('error', $msg);
            break;

            case "InvalidArguments":
                return Helper::response('error', 'Invalid arguments sent. Please try again.');
            break;

            default:
                return Helper::response('error', "Server returned an unknown error", $killedPlayer);
        }
    }

    public function postKillAll()
    {
        if( !Entrust::can('scoreboard.nuke') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $message = Input::get('message', 'Slain by an admin');

        $playerMessage = sprintf("You were slain by an admin. Reason: %s", $message);

        $killedPlayers = [];

        $this->players = self::_buildPlayerListing();

        foreach($this->players as $player)
        {
            $log = self::_log(3, $player, $message);

            $killedPlayer = $this->conn->adminKillPlayer( $player );
            $this->conn->adminSayMessageToPlayer(  $player, $playerMessage );

            $killedPlayers[] = [
                'player' => $player,
                'server_response' => $killedPlayer,
                'record' => $log
            ];
        }

        return Helper::response('success', "All players have been slain.", $killedPlayers);
    }

    public function postPunish()
    {
        if( !Entrust::can('scoreboard.punish') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $message = Input::get('message');

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $pastPunish = Record::where('command_type', 9)->where('record_time', '>=', Carbon::now()->subSeconds(20))->where('target_name', $player)->count();

        if($pastPunish > 0)
            return Helper::response('error', $player . ' has already been punished in the past 20 seconds');

        $log = self::_log(9, $player, $message, 'N');

        $msg = sprintf("%s was punished. You will be notified when punishment takes effect.", $log->target_name);

        $datetime = Helper::UTCToLocal($log->record_time)->format('M j, Y g:ia T');

        $data = array(
            'record'   => $log,
            'datetime' => $datetime
        );

        return Helper::response('success', $msg, $data);
    }

    public function postCheckPunishRecord()
    {
        if( !Entrust::can('scoreboard.punish') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $record_id = Input::get('record_id');

        $record = Record::find($record_id);

        if($record->adkats_read == 'Y')
        {
            $msg = sprintf("%s punishment was %s for %s", $record->target_name, $record->cmdaction->command_name, $record->record_message);
            return Helper::response('success', $msg);
        }
        else
        {
            return Helper::response('error', 'AdKats has not modified record yet. Try again in 10 seconds', ['record_id' => $record_id]);
        }
    }

    public function postForgive()
    {
        if( !Entrust::can('scoreboard.forgive') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $message = Input::get('message');

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $log = self::_log(10, $player, $message, 'N');

        $msg = sprintf("%s was forgiven.", $log->target_name);

        $datetime = Helper::UTCToLocal($log->record_time)->format('M j, Y g:ia T');

        $data = array(
            'record'   => $log,
            'datetime' => $datetime
        );

        return Helper::response('success', $msg, $data);
    }

    public function postMute()
    {
        if( !Entrust::can('scoreboard.pmute') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $message = Input::get('message');

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $log = self::_log(11, $player, $message, 'N');

        $msg = sprintf("%s was muted.", $log->target_name);

        $datetime = Helper::UTCToLocal($log->record_time)->format('M j, Y g:ia T');

        $data = array(
            'record'   => $log,
            'datetime' => $datetime
        );

        return Helper::response('success', $msg, $data);
    }

    public function postTeamSwap()
    {
        if( !Entrust::can('scoreboard.team') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $message = "ForceMovePlayer";

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $swapPlayer = $this->conn->adminMovePlayerSwitchTeam($player, TRUE);

        switch($swapPlayer)
        {
            case "CommandIsReadOnly":
                return Helper::response('error', 'You cannot team swap player on an offical server.');
            break;

            case "InvalidArguments":
                return Helper::response('error', 'Invalid arguments sent.');
            break;

            case "InvalidTeamId":
                return Helper::response('error', 'Invalid team id');
            break;

            case "InvalidPlayerName":
                return Helper::response('error', 'Invalid player name');
            break;

            case "InvalidForceKill":
                return Helper::response('error', 'Invalid force kill');
            break;

            case "PlayerNotDead":
                return Helper::response('error', 'Player is not dead.');
            break;

            case "SetTeamFailed":
                return Helper::response('error', 'Team set failure');
            break;

            case "SetSquadFailed":
                return Helper::response('error', 'Squad set failure');
            break;

            case "OK":
                $log = self::_log(15, $player, $message);
                return Helper::response('success', $player . ' was switched to the opposing team');
            break;
        }
    }

    public function postSquadSwap()
    {
        if( !Entrust::can('scoreboard.squad') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $newSquad = Input::get('newSquad');

        $message = "ForceMoveSquadPlayer - " . BFHelper::squad($newSquad);

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $swapSquadPlayer = $this->conn->adminMovePlayerSwitchSquad($player, $newSquad, TRUE);

        switch($swapSquadPlayer)
        {
            case "CommandIsReadOnly":
                return Helper::response('error', 'You cannot team swap player on an offical server.');
            break;

            case "InvalidArguments":
                return Helper::response('error', 'Invalid arguments sent.');
            break;

            case "InvalidTeamId":
                return Helper::response('error', 'Invalid team id');
            break;

            case "InvalidPlayerName":
                return Helper::response('error', 'Invalid player name');
            break;

            case "InvalidForceKill":
                return Helper::response('error', 'Invalid force kill');
            break;

            case "PlayerNotDead":
                return Helper::response('error', 'Player is not dead.');
            break;

            case "SetTeamFailed":
                return Helper::response('error', 'Team set failure');
            break;

            case "SetSquadFailed":
                return Helper::response('error', 'Squad set failure');
            break;

            case "OK":
                $log = self::_log(12, $player, $message);
                $msg = sprintf("%s was switch to %s squad", $player, BFHelper::squad($newSquad));
                return Helper::response('success', $msg);
            break;
        }
    }

    public function postTempBan()
    {
        if( !Entrust::can('scoreboard.tban') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $banDuration = Input::get('duration');

        if($banDuration > $this->maxTbanDuration)
        {
            $banDuration = $this->maxTbanDuration;
        }

        $message = Input::get('message');

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $playerObject = Player::where('GameID', $this->_gameid)->where('SoldierName', $player)->first();

        $log = self::_log(7, $player, $message);

        $banRecord = self::_createBanRecord($log, $playerObject, 7, $banDuration);

        $kickMessage = self::_createBanKickMessage($banRecord, $log);

        $kickedPlayer = $this->conn->adminKickPlayerWithReason($player, $kickMessage);

        $msgToAll = sprintf("Enforcing ban on %s for %s", $player, $message);
        $banEnforceMessage = $this->conn->adminSayMessageToAll($msgToAll);

        return Helper::response('success', $msgToAll, [
            'record'              => $log,
            'ban_record'          => $banRecord,
            'player_kick_message' => $kickMessage,
            'server_response'     => $kickedPlayer
        ]);
    }

    public function postPermaBan()
    {
        if( !Entrust::can('scoreboard.ban') )
            return Helper::response('error', 'Access Denied! You do not have permission to preform that action.', [], 401);

        $_players = self::_buildPlayerListing();

        $player = head($this->players);

        $message = Input::get('message');

        if(!in_array($player, $_players))
            return Helper::response('error', $player . ' is not in the server.');

        $playerObject = Player::where('GameID', $this->_gameid)->where('SoldierName', $player)->first();

        $log = self::_log(8, $player, $message);

        $banRecord = self::_createBanRecord($log, $playerObject, 8);

        $kickMessage = self::_createBanKickMessage($banRecord, $log);

        $kickedPlayer = $this->conn->adminKickPlayerWithReason($player, $kickMessage);

        $msgToAll = sprintf("Enforcing ban on %s for %s", $player, $message);
        $banEnforceMessage = $this->conn->adminSayMessageToAll($msgToAll);

        return Helper::response('success', $msgToAll, [
            'record'              => $log,
            'ban_record'          => $banRecord,
            'player_kick_message' => $kickMessage,
            'server_response'     => $kickedPlayer
        ]);
    }
}
