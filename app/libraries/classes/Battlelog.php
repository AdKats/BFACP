<?php namespace ADKGamers\Webadmin\Libs;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Battlefield\Player;
use ADKGamers\Webadmin\Models\Battlefield\Record;
use ADKGamers\Webadmin\Models\Battlefield\Server;
use ADKGamers\Webadmin\Libs\Helpers\Main AS Helper;
use ADKGamers\Webadmin\Models\Battlefield\Battlelog AS Blog;
use Requests;
use WebadminException, Requests_Exception;
use Carbon\Carbon;

class Battlelog
{
    // URL to battlelog
    const BATTLELOG   = "http://battlelog.battlefield.com/";

    /**
     * Instance of \ADKGamers\Webadmin\Models\Battlefield\Player
     * @var object
     */
    private $soldier;

    /**
     * Battlelog Profile
     * @var array
     */
    public $BLProfile;

    public function __construct(Player $soldier)
    {
        $this->soldier = $soldier;
        $this->getProfile();
    }

    public function saveToDB()
    {
        if($this->isFound())
        {
            $this->soldierNameUpdateCheck();
            return Blog::firstOrCreate(array('player_id' => $this->soldier->PlayerID, 'persona_id' => $this->getPersonaId()));
        }
        else
        {
            return FALSE;
        }
    }

    private function soldierNameUpdateCheck()
    {
        $player = Player::find($this->soldier->PlayerID);

        $server = Server::where('ConnectionState', 'on')->where('GameID', $this->soldier->GameID)->take(1)->first();

        $clantag = self::getClanTag();

        if($this->BLProfile['personaName'] != $player->SoldierName && $server)
        {
            $record = new Record;
            $record->server_id       = $server->ServerID;
            $record->command_type    = 48;
            $record->command_action  = 48;
            $record->command_numeric = 0;
            $record->target_name     = $this->BLProfile['personaName'];
            $record->target_id       = $player->PlayerID;
            $record->source_name     = 'BFAdminCP';
            $record->source_id       = NULL;
            $record->record_message  = $player->SoldierName;
            $record->record_time     = Carbon::now();
            $record->adkats_read     = 'Y';
            $record->adkats_web      = TRUE;
            $record->save();

            $player->SoldierName = $this->BLProfile['personaName'];
        }

        $player->ClanTag = $clantag;
        $player->save();
    }

    private function getProfile()
    {
        try
        {
            $response = Requests::post(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . '/user/' . $this->soldier->SoldierName . '/', array('X-AjaxNavigation' => 1, 'Expect' => true));

            $response_decode = json_decode($response->body, true);

            if(!isset($response_decode['context']['profilePersonas'][0]))
                throw new WebadminException("Player does not exist on battlelog for " . strtoupper($this->soldier->gameIdent()));

            foreach($response_decode['context']['profilePersonas'] as $persona)
            {
                if($persona['namespace'] == 'cem_ea_id')
                {
                    $this->BLProfile = $persona;
                    break;
                }
            }
        }
        catch(Requests_Exception $e)
        {
            throw new WebadminException($e->getMessage());
        }
        catch(WebadminException $e)
        {
            throw $e;
        }
    }

    public function isFound()
    {
        if(!array_key_exists('personaId', $this->BLProfile)) return FALSE;

        return TRUE;
    }

    public function getPersonaId()
    {
        return $this->BLProfile['personaId'];
    }

    private function getClanTag()
    {
        try
        {
            $response = Requests::post(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . '/soldier/' . $this->BLProfile['userId'] . '/stats/' . $this->getPersonaId() . '/pc/', array('X-AjaxNavigation' => 1, 'Expect' => true));

            $result = json_decode($response->body, true);

            $clantag = $result['context']['statsPersona']['clanTag'];

            if(empty($clantag)) return NULL;

            return $clantag;
        }
        catch(Requests_Exception $e)
        {
            return NULL;
        }
    }

    public function getOverviewBoxStats()
    {
        $response = Requests::post(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . '/user/overviewBoxStats/' . $this->BLProfile['userId'] . '/');

        $result = json_decode($response->body, true);

        return $result['data']['soldiersBox'];
    }

    public function getOverviewStats()
    {
        $response = Requests::get(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . "/warsawoverviewpopulate/" . $this->BLProfile['personaId'] . "/1/");

        $result = json_decode($response->body, true);

        return $result['data'];
    }

    public function getWeaponStats()
    {
        if($this->soldier->gameIdent() == 'BF3')
        {
            $response = Requests::get(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . "/weaponsPopulateStats/" . $this->BLProfile['personaId'] . "/1/");
        }
        else
        {
            $response = Requests::get(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . "/warsawWeaponsPopulateStats/" . $this->BLProfile['personaId'] . "/1/stats/");
        }

        $result = json_decode($response->body, true);

        return $result['data'];
    }

    public function getVehicleStats()
    {
        $response = Requests::get(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . "/warsawvehiclesPopulateStats/" . $this->BLProfile['personaId'] . "/1/stats/");

        $result = json_decode($response->body, true);

        return $result['data'];
    }

    public function getDetailedStats()
    {
        $response = Requests::get(self::BATTLELOG . strtolower($this->soldier->gameIdent()) . "/warsawdetailedstatspopulate/" . $this->BLProfile['personaId'] . "/1/");

        $result = json_decode($response->body, true);

        return $result['data']['generalStats'];
    }
}
