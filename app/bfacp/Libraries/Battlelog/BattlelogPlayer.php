<?php namespace BFACP\Libraries\Battlelog;

use BFACP\Battlefield\Player;
use BFACP\AdKats\Battlelog AS AdKatsBattelog;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;

class BattlelogPlayer extends Battlelog
{
    /**
     * Persona ID on Battlelog
     * @var integer
     */
    protected $persona_id = 0;

    /**
     * Persona
     * @var array
     */
    protected $persona;

    /**
     * Player Eloquent Object
     * @var BFACP\Battlefield\Player
     */
    protected $player;

    /**
     * Battlelog profile
     * @var array
     */
    protected $profile;

    /**
     * Game name
     * @var string
     */
    protected $game;

    public function __construct(Player $player)
    {
        parent::__construct();

        $this->player = $player;

        $this->game = strtolower($this->player->game->Name);

        if( ! $this->player->hasPersona() )
            $this->fetchBattlelogProfile();
        else
            $this->persona_id = (int) $this->player->battlelog->persona_id;
    }

    private function fetchBattlelogProfile()
    {
        try
        {
            $uri = sprintf('%s/user/%s', $this->game, $this->player->SoldierName);

            $request = $this->guzzle->get(static::BLOG . $uri, [
                'headers' => [
                    'X-AjaxNavigation' => TRUE
                ]
            ]);

            $response = $request->json();

            $this->profile = $response;

            if( ! isset( $this->profile['context']['profilePersonas'][0] ) )
                throw new Exception(sprintf('No player by the name "%s" exists on battlelog'));

            foreach($this->profile['context']['profilePersonas'] as $persona)
            {
                // Only match for PC persona
                if( $persona['namespace'] == 'cem_ea_id' )
                {
                    $this->persona = $persona;
                    $this->persona_id = (int) $persona['personaId'];
                    break;
                }
            }

            $isPersonaBanned = FALSE;

            foreach($this->profile['context']['soldiersBox'] as $soldier)
            {
                if( $soldier['isPersonaBanned'] == TRUE)
                {
                    $isPersonaBanned = TRUE;
                    break;
                }
            }

            if( ! $this->player->hasPersona() )
            {
                $this->player->battlelog()->save(new AdKatsBattelog([
                    'gravatar'       => $this->profile['context']['profileCommon']['user']['gravatarMd5'],
                    'persona_banned' => $isPersonaBanned,
                    'persona_id'     => $this->persona_id,
                    'user_id'        => $this->profile['context']['profileCommon']['user']['userId']
                ]));
            }
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function getWarsawOverview()
    {
        try
        {
            $uri = sprintf('%s/warsawoverviewpopulate/%u/1/', $this->game, $this->persona_id);

            $request = $this->guzzle->get(static::BLOG . $uri, [
                'headers' => [
                    'X-AjaxNavigation' => TRUE
                ]
            ]);

            $response = $request->json();

            return new Collection($response['data']);
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function getWarsawWeapons()
    {
        try
        {
            $uri = sprintf('%s/warsawWeaponsPopulateStats/%u/1/stats/', $this->game, $this->persona_id);

            $request = $this->guzzle->get(static::BLOG . $uri, [
                'headers' => [
                    'X-AjaxNavigation' => TRUE
                ]
            ]);

            $response = $request->json();

            return new Collection($response['data']['mainWeaponStats']);
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Returns player object
     * @return BFACP\Battlefield\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Simply returns the persona id
     * @return integer
     */
    public function getPersonaId()
    {
        return $this->persona_id;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function getPersona()
    {
        return $this->persona;
    }
}
