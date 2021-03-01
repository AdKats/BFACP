<?php

namespace BFACP\Battlefield;

use BFACP\Adkats\Record;
use BFACP\Elegant;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\GeoRepository;
use Exception;
use Illuminate\Support\Facades\Cache as Cache;

/**
 * Class Player.
 */
class Player extends Elegant
{
    /**
     * Should model handle timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'tbl_playerdata';

    /**
     * Table primary key.
     *
     * @var string
     */
    protected $primaryKey = 'PlayerID';

    /**
     * Fields not allowed to be mass assigned.
     *
     * @var array
     */
    protected $guarded = ['PlayerID'];

    /**
     * Date fields to convert to carbon instances.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output.
     *
     * @var array
     */
    protected $appends = ['profile_url', 'country_flag', 'country_name', 'rank_image', 'links', 'geo'];

    /**
     * Models to be loaded automatically.
     *
     * @var array
     */
    protected $with = ['game', 'battlelog'];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dogtags()
    {
        return $this->hasMany(\BFACP\Player\Dogtag::class, 'KillerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function ban()
    {
        return $this->hasOne(\BFACP\Adkats\Ban::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function stats()
    {
        return $this->hasManyThrough(\BFACP\Player\Stat::class, 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function sessions()
    {
        return $this->hasManyThrough(\BFACP\Player\Session::class, 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsGlobal()
    {
        return $this->hasOne(\BFACP\Adkats\Infractions\Overall::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsServer()
    {
        return $this->hasMany(\BFACP\Adkats\Infractions\Server::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function game()
    {
        return $this->belongsTo(\BFACP\Battlefield\Game::class, 'GameID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reputation()
    {
        return $this->hasOne(\BFACP\Battlefield\Reputation::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recordsBy()
    {
        return $this->hasMany(\BFACP\Adkats\Record::class, 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recordsOn()
    {
        return $this->hasMany(\BFACP\Adkats\Record::class, 'target_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function battlelog()
    {
        return $this->hasOne(\BFACP\Adkats\Battlelog::class, 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function specialGroups()
    {
        return $this->hasMany(\BFACP\Adkats\Special::class, 'player_id');
    }

    /**
     * Does the player have a battlelog persona id linked.
     *
     * @return bool
     */
    public function hasPersona()
    {
        return ! empty($this->battlelog);
    }

    /**
     * Checks if player has a reputation record.
     *
     * @return bool
     */
    public function hasReputation()
    {
        return ! empty($this->reputation);
    }

    /**
     * Purge the cache for the player.
     *
     * @return $this
     */
    public function forget()
    {
        Cache::forget(sprintf('api.player.%u', $this->PlayerID));
        Cache::forget(sprintf('player.%u', $this->PlayerID));

        return $this;
    }

    /**
     * Get the players aliases.
     *
     * @return array
     */
    public function aliases()
    {
        $aliases = Record::where('command_type', 48)->where('target_id',
            $this->PlayerID)->groupBy('record_message')->lists('record_message');

        return $aliases;
    }

    /**
     * Gets the URL to the players profile.
     *
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return route('player.show', [
            'id'   => $this->PlayerID,
            'name' => $this->SoldierName,
        ]);
    }

    /**
     * Get the country name.
     *
     * @return string
     */
    public function getCountryNameAttribute()
    {
        try {
            if ($this->CountryCode == '--' || empty($this->CountryCode)) {
                throw new Exception();
            }

            $cc = MainHelper::countries($this->CountryCode);

            if ($cc === null) {
                throw new Exception();
            }

            return $cc;
        } catch (Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get the country image flag.
     *
     * @return string
     */
    public function getCountryFlagAttribute()
    {
        try {
            if ($this->CountryCode == '--' || empty($this->CountryCode)) {
                throw new Exception();
            }

            $path = sprintf('images/flags/24/%s.png', strtoupper($this->CountryCode));

            if (! file_exists(sprintf('%s/%s', public_path(), $path))) {
                throw new Exception();
            }

            return $path;
        } catch (Exception $e) {
            return 'images/flags/24/_unknown.png';
        }
    }

    /**
     * Generates links to external/internal systems.
     *
     * @return array
     */
    public function getLinksAttribute()
    {
        switch ($this->game->Name) {
            case 'BFHL':
                $game = 'BFH';
                break;

            default:
                $game = $this->game->Name;
        }

        $links = [];

        // Battlelog URL
        if (is_null($this->battlelog)) {
            $links['battlelog'] = sprintf('http://battlelog.battlefield.com/%s/user/%s', strtolower($game),
                $this->SoldierName);
        } else {
            if ($game == 'BFH') {
                $links['battlelog'] = sprintf('http://battlelog.battlefield.com/%s/agent/%s/stats/%u/pc/',
                    strtolower($game), $this->SoldierName, $this->battlelog->persona_id);
            } else {
                $links['battlelog'] = sprintf('http://battlelog.battlefield.com/%s/soldier/%s/stats/%u/pc/',
                    strtolower($game), $this->SoldierName, $this->battlelog->persona_id);
            }
        }

        $links[] = [
            'chatlogs' => route('chatlog.search', ['pid' => $this->PlayerID]),
            'pbbans'   => ! empty($this->PBGUID) ? sprintf('http://www.pbbans.com/mbi-guid-search-%s.html',
                $this->PBGUID) : null,
            'fairplay' => sprintf('https://www.247fairplay.com/CheatDetector/%s', $this->SoldierName),
        ];

        $links2 = [];

        if ($game == 'BF4') {
            $links2 = [
                'aci' => sprintf('http://www.anticheatinc.net/forums/bansearch.php?search=%s&game=bf4&submit=Submit', $this->PBGUID),
                'cheatreport' => sprintf('http://bf4cr.com/?pid=&uid=%s&cnt=&startdate=', $this->SoldierName),
                'bf4db' => sprintf('https://bf4db.com/player/search?query=%s', $this->SoldierName),
                'ba' => sprintf('https://battlefield.agency/player/by-pb_guid/%s', $this->PBGUID),
            ];
        }

        $links = array_merge($links, $links[0], $links2);
        unset($links[0]);

        return $links;
    }

    /**
     * Get the rank image.
     *
     * @return string
     */
    public function getRankImageAttribute()
    {
        switch ($this->game->Name) {
            case 'BF3':
                $rank = $this->GlobalRank;

                if ($rank > 45) {
                    $path = sprintf('images/games/bf3/ranks/large/ss%u.png', $rank - 45);
                } else {
                    $path = sprintf('images/games/bf3/ranks/large/r%u.png', $rank);
                }
                break;

            case 'BF4':
                $path = sprintf('images/games/bf4/ranks/r%u.png', $this->GlobalRank);
                break;

            case 'BFHL':
                $path = sprintf('images/games/bfhl/ranks/r%u.png', $this->GlobalRank);
                break;

            default:
                $path = null;
        }

        return $path;
    }

    /**
     * Gets the geo data from ip address.
     *
     * @return null|array
     */
    public function getGeoAttribute()
    {
        if (empty($this->IP_Address)) {
            return;
        }

        try {
            $geo = app(GeoRepository::class);

            return $geo->set($this->IP_Address)->all();
        } catch (\Exception $e) {
            return;
        }
    }
}
