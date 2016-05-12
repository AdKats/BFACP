<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Adkats\Infractions\Overall;
use BFACP\Battlefield\Player;
use BFACP\Http\Controllers\Controller;

/**
 * Class InfractionsController.
 */
class InfractionsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $infractions = Overall::with('player.reputation');

        if ($this->request->has('player')) {
            $players = Player::where('SoldierName', 'LIKE', '%'.$this->request->get('player').'%')->lists('PlayerID');

            $infractions->whereIn('player_id', $players);
        }

        $infractions = $infractions->orderBy('total_points', 'desc')->orderBy('player_id', 'desc')->paginate(30);

        $page_title = trans('navigation.admin.adkats.items.infractions.title');

        return view('admin.adkats.infractions.index', compact('infractions', 'page_title'));
    }
}
