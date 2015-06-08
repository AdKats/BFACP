<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\AdKats\Special;
use BFACP\Http\Controllers\BaseController;
use Former;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
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

        $this->guzzle = \App::make('GuzzleHttp\Client');
    }

    public function index()
    {
        $players = Special::all();

        $groups = Cache::get('admin.adkats.special.groups');

        return View::make('admin.adkats.special_players.index', compact('players', 'groups'))
            ->with('page_title', Lang::get('navigation.admin.adkats.items.special_players.title'));
    }

    public function edit($id)
    {
        try {

            $player = Special::findOrFail($id);

            $groups = Cache::get('admin.adkats.special.groups');

            $page_title = Lang::get('navigation.admin.adkats.items.special_players.items.edit.title', ['id' => $id]);

            Former::populate($player);

            return View::make('admin.adkats.special_players.edit', compact('player', 'page_title', 'groups'));

        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.special_players.index')->withErrors([sprintf('Special Player ID #%u doesn\'t exist.', $id)]);
        }
    }
}
