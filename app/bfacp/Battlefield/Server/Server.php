<?php namespace BFACP\Battlefield\Server;

use BFACP\Elegant;
use BFACP\Facades\Battlefield as BattlefieldHelper;
use BFACP\Facades\Main as MainHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Server extends Elegant
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
    protected $table = 'tbl_server';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'ServerID';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['ServerID'];

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
    protected $appends = [
        'percentage',
        'ip',
        'port',
        'server_name_short',
        'in_queue',
        'maps_file_path',
        'modes_file_path',
        'squads_file_path',
        'teams_file_path',
        'current_map',
        'current_gamemode',
        'map_image_paths',
        'is_active',
        'slug',
    ];

    /**
     * The attributes excluded form the models JSON response.
     *
     * @var array
     */
    protected $hidden = ['maps_file_path', 'modes_file_path', 'squads_file_path', 'teams_file_path'];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = ['game', 'setting'];

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
    public function stats()
    {
        return $this->belongsTo('BFACP\Battlefield\Server\Stats', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scoreboard()
    {
        return $this->hasMany('BFACP\Battlefield\Scoreboard\Scoreboard', 'ServerID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setting()
    {
        return $this->hasOne('BFACP\Battlefield\Setting', 'server_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function scores()
    {
        return $this->hasMany('BFACP\Battlefield\Scoreboard\Scores', 'ServerID');
    }

    /**
     * Only return servers that should be active
     *
     * @param $query
     *
     * @return
     */
    public function scopeActive($query)
    {
        return $query->where('ConnectionState', 'on');
    }

    /**
     * Returns the server name with the strings that are
     * to be removed from it.
     *
     * @return string/null
     */
    public function getServerNameShortAttribute()
    {
        if (is_null($this->setting) || empty($this->setting->filter)) {
            return;
        }

        $strings = explode(',', $this->setting->filter);

        return preg_replace('/\s+/', ' ', trim(str_replace($strings, null, $this->ServerName)));
    }

    /**
     * Calculates how full the server is represented by a percentage
     *
     * @return float
     */
    public function getPercentageAttribute()
    {
        return MainHelper::percent($this->usedSlots, $this->maxSlots);
    }

    /**
     * Gets the IP Address
     *
     * @return string
     */
    public function getIPAttribute()
    {
        $host = explode(':', $this->IP_Address)[0];

        return gethostbyname($host);
    }

    /**
     * Gets the RCON port from the IP Address
     *
     * @return integer
     */
    public function getPortAttribute()
    {
        $port = explode(':', $this->IP_Address)[1];

        return (int)$port;
    }

    /**
     * Gets the human readable name of the current map
     *
     * @return string
     */
    public function getCurrentMapAttribute()
    {
        return BattlefieldHelper::mapName($this->mapName, $this->maps_file_path, $this->Gamemode);
    }

    /**
     * Gets the human readable name of the current mode
     *
     * @return string
     */
    public function getCurrentGamemodeAttribute()
    {
        return BattlefieldHelper::playmodeName($this->Gamemode, $this->modes_file_path);
    }

    /**
     * Gets the number of players currently in queue and caches the result for 5 minutes
     *
     * @return integer
     */
    public function getInQueueAttribute()
    {
        $result = Cache::remember('server.' . $this->ServerID . '.queue', 5, function () {
            $battlelog = App::make('BFACP\Libraries\Battlelog\BattlelogServer');

            return $battlelog->server($this)->inQueue();
        });

        return $result;
    }

    /**
     * Gets the path of the maps xml file
     *
     * @return string
     */
    public function getMapsFilePathAttribute()
    {
        $path = app_path() . DIRECTORY_SEPARATOR . 'bfacp' . DIRECTORY_SEPARATOR . 'ThirdParty' . DIRECTORY_SEPARATOR . strtoupper($this->game->Name) . DIRECTORY_SEPARATOR . 'mapNames.xml';

        return $path;
    }

    /**
     * Gets the path of the gamemodes xml file
     *
     * @return string
     */
    public function getModesFilePathAttribute()
    {
        $path = app_path() . DIRECTORY_SEPARATOR . 'bfacp' . DIRECTORY_SEPARATOR . 'ThirdParty' . DIRECTORY_SEPARATOR . strtoupper($this->game->Name) . DIRECTORY_SEPARATOR . 'playModes.xml';

        return $path;
    }

    /**
     * Gets the path of the squads xml file
     *
     * @return string
     */
    public function getSquadsFilePathAttribute()
    {
        $path = app_path() . DIRECTORY_SEPARATOR . 'bfacp' . DIRECTORY_SEPARATOR . 'ThirdParty' . DIRECTORY_SEPARATOR . strtoupper($this->game->Name) . DIRECTORY_SEPARATOR . 'squadNames.xml';

        return $path;
    }

    /**
     * Gets the path of the teams xml file
     *
     * @return string
     */
    public function getTeamsFilePathAttribute()
    {
        $path = app_path() . DIRECTORY_SEPARATOR . 'bfacp' . DIRECTORY_SEPARATOR . 'ThirdParty' . DIRECTORY_SEPARATOR . strtoupper($this->game->Name) . DIRECTORY_SEPARATOR . 'teamNames.xml';

        return $path;
    }

    /**
     * Gets the current map image banner
     *
     * @return string
     */
    public function getMapImagePathsAttribute()
    {
        $base_path = sprintf('images/games/%s/maps', strtolower($this->game->Name));

        if ($this->game->Name == 'BFHL') {
            $image = sprintf('%s.png', strtolower($this->mapName));
        } else {
            $image = sprintf('%s.jpg', strtolower($this->mapName));
        }

        $paths = [
            'large'  => sprintf('%s/large/%s', $base_path, $image),
            'medium' => sprintf('%s/medium/%s', $base_path, $image),
            'wide'   => in_array($this->game->Name, ['BF4', 'BFHL']) ? sprintf('%s/wide/%s', $base_path,
                $image) : sprintf('%s/large/%s', $base_path, $image),
        ];

        return $paths;
    }

    /**
     * Is the server enabled?
     *
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return $this->ConnectionState == 'on';
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->ServerName);
    }
}
