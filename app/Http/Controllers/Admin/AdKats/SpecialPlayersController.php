<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Adkats\Special;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class SpecialPlayersController.
 */
class SpecialPlayersController extends Controller
{
    /**
     * GuzzleHttp\Client.
     */
    protected $guzzle;

    /**
     *
     */
    public function __construct()
    {
        $this->guzzle = app('Guzzle');
    }

    /**
     * @return $this
     */
    public function index()
    {
        $players = Special::with('player', 'game', 'server')->get();

        $groups = MainHelper::specialGroups();

        return view('admin.adkats.special_players.index', compact('players', 'groups'))->with('page_title',
            trans('navigation.admin.adkats.items.special_players.title'));
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function update($id)
    {
        try {
            $groups = $this->cache->get('admin.adkats.special.groups');

            $player = Special::findOrFail($id);

            $newGroup = null;

            foreach ($groups as $group) {
                if ($group['group_key'] == $this->request->get('group')) {
                    $newGroup = $group['group_name'];
                    break;
                }
            }

            $player->player_group = $this->request->get('group');
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
