<?php

namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Game;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;

/**
 * Class ChatlogController.
 */
class ChatlogController extends Controller
{
    /**
     * @param Game   $game
     * @param Chat   $chat
     * @param Player $player
     */
    public function __construct(Game $game, Chat $chat, Player $player)
    {
        parent::__construct();

        $this->middleware('permission:chatlogs');

        $this->game = $game;
        $this->chat = $chat;
        $this->player = $player;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $games = $this->game->with([
            'servers' => function ($query) {
                $query->active();
            },
        ])->get();

        $page_title = trans('navigation.main.items.chatlogs.title');

        $chat = $this->chat->with('player', 'server')->orderBy('logDate', 'desc');

        // If the show spam checkbox was not checked then exclude it
        if (! $this->request->has('showspam')) {
            $chat = $chat->excludeSpam();
        }

        // Check if user hit the submit button
        if ($this->hasInput()) {

            // Filtering by dates
            if ($this->request->has('StartDateTime') && $this->request->has('EndDateTime')) {
                $startDate = Carbon::parse($this->request->get('StartDateTime'))->setTimezone(new \DateTimeZone('UTC'));
                $endDate = Carbon::parse($this->request->get('EndDateTime'))->setTimezone(new \DateTimeZone('UTC'));
                $chat = $chat->whereBetween('logDate', [$startDate, $endDate]);
            }

            // Specific keywords the user has typed. Each word separated by a comma.
            // This can add significant time to fetching the results
            if ($this->request->has('keywords')) {
                $keywords = array_map('trim', explode(',', $this->request->get('keywords')));

                if (MainHelper::hasFulltextSupport('tbl_chatlog', 'logMessage')) {
                    $chat = $chat->whereRaw('MATCH(logMessage) AGAINST(? IN BOOLEAN MODE)', [implode(' ', $keywords)]);
                } else {
                    $chat = $chat->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->orWhere('logMessage', 'LIKE', '%'.$keyword.'%');
                        }
                    });
                }
            }

            // Player names the user has typed. Partial names can be provided. Must be separated
            // by a comma to search multiple players.
            if ($this->request->has('players')) {
                $players = array_map('trim', explode(',', $this->request->get('players')));

                $playerIds = $this->player->where(function ($query) use ($players) {
                    foreach ($players as $player) {
                        $query->orWhere('SoldierName', 'LIKE', sprintf('%s%%', $player));
                    }
                });

                $playerIds = $playerIds->pluck('PlayerID');

                $chat = $chat->whereIn('logPlayerID', $playerIds);
            }

            if ($this->request->has('pid')) {
                if ($this->request->get('pid') > 0 && is_numeric($this->request->get('pid'))) {
                    $chat = $chat->where('logPlayerID', $this->request->get('pid'));
                }
            }

            // Filter based on server if one is selected
            if ($this->request->has('server') && is_numeric($this->request->get('server')) && $this->request->get('server') > 0) {
                $chat = $chat->where('ServerID', $this->request->get('server'));
            }
        }

        // Return paginated results
        $chat = $chat->simplePaginate(60);

        return view('chatlogs', compact('games', 'chat', 'page_title'));
    }

    /**
     * Checks if we have any of the required inputs.
     *
     * @return bool
     */
    private function hasInput()
    {
        return $this->request->has('server') || $this->request->has('players') || $this->request->has('pid') || $this->request->has('keywords') || $this->request->has('showspam') || ($this->request->has('StartDateTime') && $this->request->has('EndDateTime'));
    }
}
