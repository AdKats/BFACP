<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Special;
use BFACP\Http\Controllers\BaseController;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use MainHelper;

class SpecialPlayersController extends BaseController
{
    /**
     * GuzzleHttp\Client
     */
    protected $guzzle;

    public function __construct()
    {
        parent::__construct();
        $this->guzzle = \App::make('GuzzleHttp\Client');
    }

    public function index()
    {
        $players = Special::all();
        $groups  = Cache::get('admin.adkats.special.groups');
        return View::make('admin.adkats.special_players.index', compact('players', 'groups'))
            ->with('page_title', Lang::get('navigation.admin.adkats.items.special_players.title'));
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
