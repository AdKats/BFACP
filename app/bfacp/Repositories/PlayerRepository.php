<?php namespace BFACP\Repositories;

use BFACP\Adkats\Record;
use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Player;
use BFACP\Exceptions\PlayerNotFoundException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class PlayerRepository extends BaseRepository
{
    /**
     * Eager loading options
     *
     * @var array
     */
    private $opts = [
        'ban.previous',
        'reputation',
        'infractionsGlobal',
        'infractionsServer.server',
        'dogtags.victim',
        'stats.weapons.weapon',
        'stats.server',
    ];

    /**
     * Returns a paginate result of all players
     *
     * @param int  $limit
     * @param null $names
     *
     * @return \Illuminate\Pagination\Paginator
     */
    public function getAllPlayers($limit = 100, $names = null)
    {
        if ($limit === false || $limit > 100) {
            $limit = 100;
        }

        $query = Player::with('ban', 'infractionsGlobal', 'infractionsServer.server', 'reputation');

        $names = new Collection(explode(',', $names));

        $soldierNames = [];

        if (!empty($names)) {
            $query->where(function ($q) use (&$names, &$soldierNames) {
                $names->each(function ($name) use (&$q, &$soldierNames) {
                    // Checks if string is an EAGUID
                    if (preg_match('/^EA_([0-9A-Z]{32}+)$/', $name, $matches)) {
                        $eaguid = sprintf('EA_%s', $matches[1]);
                        $q->orWhere('EAGUID', '=', $eaguid);
                    } // Checks if string is a PBGUID
                    elseif (preg_match('/^([a-f0-9]+)$/', $name, $matches)) {
                        $pbguid = trim($matches[1]);
                        $q->orWhere('PBGUID', '=', $pbguid);
                    } // Checks if string is an IPv4 Address
                    elseif (preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|[0-9])$/",
                        $name, $matches)) {
                        $ip = trim($name);
                        $q->orWhere('IP_Address', '=', $ip);
                    } // Checks if string is a player name
                    elseif (preg_match("/^\*?([a-zA-Z0-9\_\-\|]+)$/", $name, $matches)) {
                        if (substr($matches[0], 0, 1) === '*') {
                            $name = sprintf('%%%s%%', $matches[1]);
                        } else {
                            $name = sprintf('%s%%', $matches[1]);
                        }

                        if (isset($matches[1]) && !empty($matches[1])) {
                            $q->orWhere('SoldierName', 'LIKE', $name);
                            $soldierNames[] = $name;
                        }
                    }
                });
            });

            if (!empty($soldierNames)) {
                $playerIds = Player::whereIn('PlayerID', function ($q) use (&$soldierNames) {
                    $q->select('target_id')->from('adkats_records_main')->where(function ($q2) use (&$soldierNames) {
                        foreach ($soldierNames as $name) {
                            $q2->orWhere('record_message', 'LIKE', $name);
                        }
                    })->where('command_type', 48);
                })->lists('PlayerID');

                if (!empty($playerIds)) {
                    $query->orWhereIn('PlayerID', $playerIds);
                }
            }

            return $query->paginate($limit);
        }

        $query->orderBy('PlayerID', 'ASC');

        return $query->simplePaginate($limit);
    }

    /**
     * Returns a player by their ID
     *
     * @param  integer $id Database Player ID
     *
     * @return object
     */
    public function getPlayerById($id)
    {
        try {
            $player = Player::with($this->opts)->findOrFail($id);

            App::make('BFACP\Libraries\Reputation')->setPlayer($player)->createOrUpdate();

            return $player;

        } catch (ModelNotFoundException $e) {
            throw new PlayerNotFoundException(404, 'Player Not Found');
        }
    }

    /**
     * Returns the player with the givin guid
     *
     * @param  string $guid EA GUID
     *
     * @return object
     */
    public function getPlayerByGuid($guid)
    {
        $player = Player::with($this->opts)->where('EAGUID', $guid)->get();

        if ($player->count() > 0) {
            return $player;
        }

        throw new PlayerNotFoundException(404, 'Player Not Found');
    }

    /**
     * Gets the total number of players in the database
     *
     * @return integer
     */
    public function getPlayerCount()
    {
        $count = Player::count('PlayerID');

        return intval($count);
    }

    /**
     * Gets the total number of players seen from each country
     *
     * @return array
     */
    public function getPlayersSeenByCountry()
    {
        $result = DB::table('tbl_playerdata')->whereNotIn('CountryCode',
            ['', '--'])->whereNotNull('CountryCode')->whereIn('tbl_playerdata.PlayerID', function ($query) {
            $query->select('tbl_server_player.PlayerID')->from('tbl_server_player')->whereIn('tbl_server_player.StatsID',
                function ($query) {
                    $query->select('StatsID')->from('tbl_playerstats')->where('LastSeenOnServer', '>=',
                        Carbon::now()->subDay());
                });
        })->groupBy('CountryCode')->select(DB::raw('UPPER(`CountryCode`) AS `CountryCode`, COUNT(`tbl_playerdata`.`PlayerID`) AS `total`'))->get();

        $result = new Collection($result);

        return $result;
    }

    /**
     * Returns the player record history
     *
     * @param  integer $id    Player ID
     * @param  integer $limit Results to return
     *
     * @return object
     */
    public function getPlayerRecords($id, $limit = 25)
    {
        $records = Record::with('target', 'source', 'type', 'action', 'server')->orderBy('record_time',
            'desc')->whereNotIn('command_type', [48, 49, 85, 86])->where(function ($query) use ($id) {
            $query->where('target_id', $id);
            $query->orWhere('source_id', $id);
        });

        // If a command id is present we are going to only pull records
        // that have the specific id
        if (Input::has('cmdid')) {
            $cmdid = Input::get('cmdid', null);

            // Make sure the input is a number and greater than zero
            if (!is_null($cmdid) && is_numeric($cmdid) && $cmdid > 0) {
                $records->where(function ($query) use ($cmdid) {
                    $query->where('command_type', $cmdid);
                    $query->orWhere('command_action', $cmdid);
                });
            }
        }

        return $records->paginate($limit);
    }

    /**
     * Returns the player session history
     *
     * @param  integer $id Player ID
     *
     * @return object
     */
    public function getPlayerSessions($id)
    {
        try {
            $sessions = Player::findOrFail($id)->sessions()->orderBy('StartTime', 'desc')->get();

            return $sessions;
        } catch (ModelNotFoundException $e) {
            throw new PlayerNotFoundException(404, 'Player Not Found');
        }
    }

    /**
     * Returns the player chatlogs
     *
     * @param  integer $id    Player ID
     * @param  integer $limit Results to return
     *
     * @return object
     */
    public function getPlayerChat($id, $limit = 30)
    {
        $chatlogs = Chat::with('server')->where('logPlayerID', $id)->excludeSpam()->orderBy('logDate', 'desc');

        // If a server is specifed then we only pull logs from that server
        if (Input::has('server')) {
            $serverId = Input::get('server', null);

            if (!is_null($serverId) && is_numeric($serverId) && $serverId > 0) {
                $chatlogs->where('ServerID', $serverId);
            }
        }

        // If user has entered keywords only pull logs that contain those keywords
        if (Input::has('keywords')) {
            $keywords = trim(Input::get('keywords', null));

            if (!is_null($keywords) && $keywords != '') {

                // Remove spaces before and after the comma
                $keywords = preg_replace('/\s*,\s*/', ',', $keywords);

                // Explode into an array
                $keywords = explode(',', $keywords);

                $chatlogs->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere('logMessage', 'LIKE', '%' . $keyword . '%');
                    }
                });
            }
        }

        return $chatlogs->paginate($limit);
    }

    /**
     * Sets which relations should be returned
     *
     * @param  array $opts
     * @param  bool  $custom
     *
     * @return $this
     */
    public function setopts($opts = [], $custom = false)
    {
        if (empty($opts) || $custom) {

            if ($custom) {
                $this->opts = $opts;
            }

            return $this;
        }

        if (is_string($opts)) {
            $opts = explode(',', $opts);
        }

        if (!in_array('bans', $opts)) {
            unset($this->opts[0]);
        }

        if (!in_array('reputation', $opts)) {
            unset($this->opts[1]);
        }

        if (!in_array('infractions', $opts)) {
            unset($this->opts[2], $this->opts[3]);
        }

        if (!in_array('stats', $opts)) {
            unset($this->opts[4], $this->opts[5], $this->opts[6]);
        }

        if (in_array('none', $opts)) {
            $this->opts = [];
        }

        return $this;
    }
}
