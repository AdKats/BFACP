<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Special;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class SpecialPlayersController extends BaseController
{
    /**
     * GuzzleHttp\Client
     */
    protected $guzzle;

    public function __construct()
    {
        parent::__construct();
        $this->guzzle = App::make('GuzzleHttp\Client');
    }

    public function index()
    {
        $players = Special::with('player', 'game', 'server')->get();

        $groups = Cache::remember('admin.adkats.special.groups', 60 * 24, function () {
            try {
                $request = $this->guzzle->get('https://raw.githubusercontent.com/AdKats/AdKats/master/adkatsspecialgroups.json');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            } catch (RequestException $e) {
                $request = $this->guzzle->get('http://api.gamerethos.net/adkats/fetch/specialgroups');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            }

            return new Collection($data);
        });

        return View::make('admin.adkats.special_players.index', compact('players', 'groups'))->with('page_title',
            Lang::get('navigation.admin.adkats.items.special_players.title'));
    }

    public function update($id)
    {
        try {
            $groups = Cache::get('admin.adkats.special.groups');

            $player = Special::findOrFail($id);

            foreach ($groups as $group) {
                if ($group['group_key'] == Input::get('group')) {
                    $newGroup = $group['group_name'];
                    break;
                }
            }

            $player->player_group = Input::get('group');
            $player->save();

            if (is_null($player->player)) {
                $soldierName = $player->player_identifier;
            } else {
                $soldierName = $player->player->SoldierName;
            }

            $message = sprintf('%s group has been changed to %s.', $soldierName, $newGroup);

            return MainHelper::response(null, $message);
        } catch (ModelNotFoundException $e) {
            $message = sprintf('No player found with special id of %u', $id);
            return MainHelper::response(null, $message, 'error', 404);
        } catch (Exception $e) {
            return MainHelper::response($e, $e->getMessage(), 'error', 500);
        }
    }
}
