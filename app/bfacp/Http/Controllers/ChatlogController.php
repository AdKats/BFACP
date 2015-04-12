<?php namespace BFACP\Http\Controllers;

use BFACP\Battlefield\Chat;
use BFACP\Battlefield\Game;
use BFACP\Battlefield\Player;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class ChatlogController extends BaseController
{
    public function __construct(Game $game, Chat $chat, Player $player)
    {
        parent::__construct();

        $this->game   = $game;
        $this->chat   = $chat;
        $this->player = $player;
    }

    public function index()
    {
        $games = $this->game->with(['servers' => function ($query) {
            $query->active();
        }])->get();

        $page_title = Lang::get('navigation.main.items.chatlogs.title');

        $chat = $this->chat->with('player', 'server')->orderBy('logDate', 'desc');

        // Check if user hit the submit button
        if ($this->hasInput()) {

            // If the hide spam checkbox was checked then exclude it
            if (!Input::has('showspam')) {
                $chat = $chat->excludeSpam();
            }

            // Filtering by dates
            if (Input::has('StartDateTime') && Input::has('EndDateTime')) {
                $startDate = Carbon::parse(Input::get('StartDateTime'));
                $endDate   = Carbon::parse(Input::get('EndDateTime'));
                $chat      = $chat->whereBetween('logDate', [$startDate, $endDate]);
            }

            // Specific keywords the user has typed. Each word seprated by a comma.
            // This can add significant time to fetching the results
            if (Input::has('keywords')) {
                $keywords = array_map('trim', explode(',', Input::get('keywords')));

                $chat = $chat->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere('logMessage', 'LIKE', sprintf('%s%%', $keyword));
                    }
                });
            }

            // Player names the user has typed. Partal names can be provided. Must be seprated
            // by a comma to search multiple players.
            if (Input::has('players')) {
                $players = array_map('trim', explode(',', Input::get('players')));

                $playerIds = $this->player->where(function ($query) use ($players) {
                    foreach ($players as $player) {
                        $query->orWhere('SoldierName', 'LIKE', sprintf('%s%%', $player));
                    }
                });

                $playerIds = $playerIds->lists('PlayerID');

                $chat = $chat->whereIn('logPlayerID', $playerIds);
            }

            // Filter based on server if one is selected
            if (Input::has('server') && is_numeric(Input::get('server')) && Input::get('server') > 0) {
                $chat = $chat->where('ServerID', Input::get('server'));
            }
        }

        // Return paginated results
        $chat = $chat->simplePaginate(60);

        return View::make('chatlogs', compact('games', 'chat', 'page_title'));
    }

    /**
     * Checks if we have any of the required inputs
     * @return boolean
     */
    private function hasInput()
    {
        return Input::has('server') ||
        Input::has('players') ||
        Input::has('keywords') ||
        Input::has('nospam') ||
        (Input::has('StartDateTime') && Input::has('EndDateTime'));
    }
}
