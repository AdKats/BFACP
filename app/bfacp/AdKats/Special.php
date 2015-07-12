<?php namespace BFACP\AdKats;

use BFACP\Elegant;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Special extends Elegant
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
    protected $table = 'adkats_specialplayers';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'specialplayer_id';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['specialplayer_id'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = ['player_effective', 'player_expiration'];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['effective_stamp', 'expiration_stamp', 'group'];

    /**
     * Models to be loaded automatically
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function player()
    {
        return $this->belongsTo('BFACP\Battlefield\Player', 'player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function game()
    {
        return $this->belongsTo('BFACP\Battlefield\Game', 'player_game');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function server()
    {
        return $this->belongsTo('BFACP\Battlefield\Server', 'server_id');
    }

    public function getEffectiveStampAttribute()
    {
        return $this->player_effective->toIso8601String();
    }

    public function getExpirationStampAttribute()
    {
        return $this->player_expiration->toIso8601String();
    }

    public function getGroupAttribute()
    {
        $groups = Cache::remember('admin.adkats.special.groups', 60 * 24, function () {
            $guzzle = App::make('GuzzleHttp\Client');
            try {
                $request = $guzzle->get('https://raw.githubusercontent.com/AdKats/AdKats/master/adkatsspecialgroups.json');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            } catch (RequestException $e) {
                $request = $guzzle->get('http://api.gamerethos.net/adkats/fetch/specialgroups');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            }

            return new Collection($data);
        });

        foreach ($groups as $group) {
            if ($group['group_key'] == $this->player_group) {
                return (object)$group;
            }
        }
    }
}
