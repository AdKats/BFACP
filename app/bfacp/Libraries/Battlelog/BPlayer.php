<?php namespace BFACP\Libraries\Battlelog;

use BFACP\Battlefield\Player;
use BFACP\Exceptions\BattlelogException;
use Illuminate\Database\Eloquent\Collection;
use MainHelper;

class BPlayer extends Battlelog
{
    /**
     * Persona ID
     * @var integer
     */
    public $personaID = 0;

    /**
     * Persona User ID
     * @var integer
     */
    public $personaUserID = 0;

    /**
     * Persona Gravatar MD5 Hash
     * @var string
     */
    public $personaGravatar = '';

    /**
     * Player Object
     * @var BFACP\Battlefield\Player
     */
    public $player;

    /**
     * Profile object
     * @var array
     */
    public $profile;

    /**
     * Name of game
     * @var string
     */
    public $game = '';

    public function __construct(Player $player)
    {
        parent::__construct();

        $this->player = $player;

        switch ($this->player->game->Name) {
            case 'BFHL':
                $this->game = 'bfh';
                break;

            default:
                $this->game = strtolower($this->player->game->Name);
        }

        // Always call fetch profile if no persona already exists for the player
        if (!$this->player->hasPersona()) {
            $this->fetchProfile();
        } else {
            $this->personaID       = $this->player->battlelog->persona_id;
            $this->personaUserID   = $this->player->battlelog->user_id;
            $this->personaGravatar = $this->player->battlelog->gravatar;
        }
    }

    /**
     * Fetchs the players battlelog profile
     * @return mixed
     */
    public function fetchProfile()
    {
        // Generate URI for request
        $uri = sprintf($this->uris['generic']['profile'], $this->game, $this->player->SoldierName);

        // Send request
        $this->profile = $this->sendRequest($uri);

        // If the persona object is empty throw a BattlelogException
        if (empty($this->profile['context']['profilePersonas'])) {
            throw new BattlelogException(404, sprintf('No player by the name "%s" exists on battlelog.', $this->player->SoldierName));
        }

        // Set the gravtar of the player
        $this->personaGravatar = $this->profile['context']['profileCommon']['user']['gravatarMd5'];

        // Assign array of personas
        $personas = $this->profile['context']['profilePersonas'];

        // Loop over the personas and find a persona for the PC version only
        foreach ($personas as $persona) {
            // PC Namespace
            if ($persona['namespace'] == 'cem_ea_id') {
                $this->personaID     = $persona['personaId'];
                $this->personaUserID = $persona['userId'];
                break;
            }
        }

        // If we don't have an existing stored persona we need to create one
        if (!$this->player->hasPersona()) {
            // Assign a relationship for them and save to the database
            $this->player->battlelog()->save(new \BFACP\AdKats\Battlelog([
                'gravatar'       => $this->personaGravatar,
                'persona_banned' => false,
                'persona_id'     => $this->personaID,
                'user_id'        => $this->personaUserID
            ]));

            // Reload the relationship
            $this->player->load('battlelog');
        }

        return $this;
    }

    /**
     * Gets the player weapon stats
     * @return array
     */
    public function getWeaponStats()
    {
        // Generate URI for request
        $uri = sprintf($this->uris[$this->game]['weapons'], $this->game, $this->personaID);

        // Send request
        $results = $this->sendRequest($uri)['data'];

        // Create weapons array
        $weapons = new Collection;

        // Loop over the weapons and add them to the weapons array
        foreach ($results['mainWeaponStats'] as $weapon) {

            if ($this->game == 'bf3') {
                $weaponURI = sprintf('%s/soldier/%s/iteminfo/%s/%u/pc/',
                    $this->game,
                    $this->player->SoldierName,
                    strtolower($weapon['slug']),
                    $this->personaID);
            } else {
                $weaponURI = sprintf('%s/soldier/%s/weapons/%u/pc/#%s',
                    $this->game,
                    $this->player->SoldierName,
                    $this->personaID,
                    strtolower($weapon['slug']));
            }

            $weapons->push([
                'slug'         => $weapon['slug'],
                'category'     => $weapon['category'],
                'headshots'    => $weapon['headshots'],
                'kills'        => $weapon['kills'],
                'deaths'       => $weapon['deaths'],
                'score'        => $weapon['score'],
                'fired'        => $weapon['shotsFired'],
                'hit'          => $weapon['shotsHit'],
                'timeEquipped' => $weapon['timeEquipped'],
                'accuracy'     => MainHelper::percent($weapon['shotsHit'], $weapon['shotsFired']),
                'kpm'          => MainHelper::divide($weapon['kills'], MainHelper::divide($weapon['timeEquipped'], 60)),
                'hskp'         => MainHelper::percent($weapon['headshots'], $weapon['kills']),
                'dps'          => MainHelper::percent($weapon['kills'], $weapon['shotsHit']),
                'weapon_link'  => static::BLOG . $weaponURI
            ]);
        }

        return $weapons;
    }

    /**
     * Gets the player overview stats
     * @return array
     */
    public function getOverviewStats()
    {
        // Generate URI for request
        $uri = sprintf($this->uris[$this->game]['overview'], $this->game, $this->personaID);

        // Send request
        $results = $this->sendRequest($uri)['data'];

        $overview = new Collection($results['overviewStats']);

        return $overview;
    }

    /**
     * Gets the player vehicle stats
     * @return array
     */
    public function getVehicleStats()
    {
        // Generate URI for request
        $uri = sprintf($this->uris[$this->game]['vehicles'], $this->game, $this->personaID);

        // Send request
        $results = $this->sendRequest($uri)['data'];

        // Create vehicles array
        $vehicles = new Collection;

        foreach ($results['mainVehicleStats'] as $vehicle) {
            $vehicles->push([
                'slug'         => $vehicle['slug'],
                'code'         => $vehicle['code'],
                'category'     => $vehicle['category'],
                'kills'        => $vehicle['kills'],
                'score'        => array_key_exists('score', $vehicle) ? $vehicle['score'] : null,
                'timeEquipped' => $vehicle['timeIn'],
                'serviceStars' => $vehicle['serviceStars'],
                'kpm'          => MainHelper::divide($vehicle['kills'], MainHelper::divide($vehicle['timeIn'], 60))
            ]);
        }

        return $vehicles;
    }

    /**
     * Gets the player battle reports. Only works if game is bf4 or bfh
     * and the player has publicly visible reports.
     * @return array
     */
    public function getBattleReports()
    {
        if ($this->game == 'bf3') {
            throw new BattlelogException(404, 'Reports for Battlefield 3 are not supported.');
        }

        // Generate URI for request
        $uri = sprintf($this->uris[$this->game]['battlereports'], $this->game, $this->personaID);

        // Send request
        $results = $this->sendRequest($uri)['data'];

        $battlereports = new Collection($results['gameReports']);

        return $battlereports;
    }
}
