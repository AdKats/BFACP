<?php namespace BFACP\Battlefield;

use BFACP\Elegant;
use BFACP\Facades\Main as MainHelper;
use Exception;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Route;

class Player extends Elegant
{
    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'tbl_playerdata';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'PlayerID';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['PlayerID'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['profile_url', 'country_flag', 'country_name', 'rank_image', 'links'];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = ['game', 'battlelog'];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dogtags()
    {
        return $this->hasMany('BFACP\Player\Dogtag', 'KillerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function ban()
    {
        return $this->hasOne('BFACP\Adkats\Ban', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function stats()
    {
        return $this->hasManyThrough('BFACP\Player\Stat', 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function sessions()
    {
        return $this->hasManyThrough('BFACP\Player\Session', 'BFACP\Player\Server', 'PlayerID', 'StatsID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsGlobal()
    {
        return $this->hasOne('BFACP\Adkats\Infractions\Overall', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function infractionsServer()
    {
        return $this->hasMany('BFACP\Adkats\Infractions\Server', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function game()
    {
        return $this->belongsTo('BFACP\Battlefield\Game', 'GameID')->remember(30);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function reputation()
    {
        return $this->hasOne('BFACP\Battlefield\Reputation', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recordsBy()
    {
        return $this->hasMany('BFACP\Adkats\Record', 'source_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recordsOn()
    {
        return $this->hasMany('BFACP\Adkats\Record', 'target_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function battlelog()
    {
        return $this->hasOne('BFACP\Adkats\Battlelog', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function specialGroups()
    {
        return $this->hasMany('BFACP\Adkats\Special', 'player_id');
    }

    /**
     * Does the player have a battlelog persona id linked
     *
     * @return boolean
     */
    public function hasPersona()
    {
        return !empty($this->battlelog);
    }

    /**
     * Checks if player has a reputation record
     *
     * @return boolean
     */
    public function hasReputation()
    {
        return !empty($this->reputation);
    }

    /**
     * Purge the cache for the player
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
     * Gets the URL to the players profile
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
     * Get the country name
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
     * Get the country image flag
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

            if (!file_exists(sprintf('%s/%s', public_path(), $path))) {
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

        if ($game == 'BF4') {
            try {
                if (Route::currentRouteName() != 'player.show') {
                    throw new Exception();
                }

                $request = App::make('guzzle')->get(sprintf('http://api.bf4db.com/api-player.php?%s', http_build_query([
                    'format' => 'json',
                    'guid'   => $this->EAGUID,
                ])), [
                    'connect_timeout' => 5,
                ]);

                $response = $request->json();

                if ($response['type'] != 'error') {
                    $bf4db_profile = [
                        'url'        => $response['data']['bf4db_url'],
                        'cheatscore' => $response['data']['cheatscore'],
                    ];
                } else {
                    throw new Exception();
                }
            } catch (Exception $e) {
                $bf4db_profile = [
                    'url'        => sprintf('http://bf4db.com/players?name=%s', $this->SoldierName),
                    'cheatscore' => null,
                ];
            }
        }

        $links[] = [
            'bf3stats' => $game == 'BF3' ? sprintf('http://bf3stats.com/stats_pc/%s', $this->SoldierName) : null,
            'bf4stats' => $game == 'BF4' ? sprintf('http://bf4stats.com/pc/%s', $this->SoldierName) : null,
            'bfhstats' => $game == 'BFH' ? sprintf('http://bfhstats.com/pc/%s', $this->SoldierName) : null,
            'istats'   => sprintf('http://i-stats.net/index.php?action=pcheck&player=%s&game=%s&sub=Check+Player',
                $this->SoldierName, $game),
            'metabans' => sprintf('http://metabans.com/search/?phrase=%s', $this->SoldierName),
            'bf4db'    => $game == 'BF4' ? $bf4db_profile : null,
            'chatlogs' => route('chatlog.search', ['pid' => $this->PlayerID]),
            'pbbans'   => !empty($this->PBGUID) ? sprintf('http://www.pbbans.com/mbi-guid-search-%s.html',
                $this->PBGUID) : null,
        ];

        $links = array_merge($links, $links[0]);
        unset($links[0]);

        return $links;
    }

    /**
     * Get the rank image
     *
     * @return string
     */
    public function getRankImageAttribute()
    {
        switch ($this->game->Name) {
            case 'BF3':
                $rank = $this->GlobalRank;

                if ($rank > 45) {
                    if ($rank > 100) {
                        $rank = 100;
                    }

                    $path = sprintf('images/games/bf3/ranks/large/ss%u.png', $rank);
                } else {
                    $path = sprintf('images/games/bf3/ranks/large/r%u.png', $this->GlobalRank);
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
}
