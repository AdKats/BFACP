<?php

namespace BFACP\Http\Controllers;

use BFACP\Adkats\Record;
use BFACP\Battlefield\Player;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\PlayerRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class PlayersController.
 */
class PlayersController extends Controller
{
    /**
     * @var PlayerRepository
     */
    private $repository;

    /**
     * Shows player listing.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing()
    {
        $page_title = $this->request->has('player') ? 'Player Search' : 'Player Listing';

        return view('player.listing', compact('page_title'));
    }

    /**
     * Shows the player profile.
     *
     * @param int    $id
     * @param string $name
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile($id, $name = '')
    {
        // Cache key
        $key = sprintf('player.%u', $id);

        // Is there already a cached version for the player
        $isCached = $this->cache->has($key);

        $this->repository = app(PlayerRepository::class);

        // Get or Set cache for player
        $player = $this->cache->remember($key, 5, function () use ($id) {
            $json = $this->repository->setopts([
                'ban.previous.server',
                'ban.record.server',
                'reputation',
                'infractionsGlobal',
                'infractionsServer.server',
                'stats.server',
                'specialGroups',
            ], true)->getPlayerById($id)->toJson();

            return json_decode($json);
        });

        $charts = $this->cache->remember(sprintf('player.%u.charts', $id), 5, function () use ($id) {
            $charts = [];
            $charts['overview'] = new Collection(DB::select(File::get(storage_path().'/sql/playerCommandOverview.sql'),
                [$id]));
            $charts['spline'] = new Collection(DB::select(File::get(storage_path().'/sql/playerCommandHistory.sql'),
                [$id]));
            $charts['aliases'] = Record::where('command_type', 48)->where('target_id',
                $id)->select(DB::raw('record_message AS `player_name`, COUNT(record_id) AS `seen`'))->groupBy('player_name')->get();

            $charts['iphistory'] = Record::where('command_type', 49)->where('target_id', $id)->where('record_message',
                '!=',
                'No previous IP on record')->select(DB::raw('record_message AS `ip`, COUNT(record_id) AS `seen`'))->groupBy('ip')->get();

            $charts['overview'] = $charts['overview']->map(function ($command) {
                return [
                    $command->label,
                    intval($command->value),
                ];
            });

            $charts['aliases'] = $charts['aliases']->map(function ($a) {
                return [
                    $a->player_name,
                    intval($a->seen),
                ];
            });

            $charts['iphistory'] = $charts['iphistory']->map(function ($ip) {
                return [
                    $ip->ip,
                    intval($ip->seen),
                ];
            });

            return $charts;
        });

        $groups = MainHelper::specialGroups($player->special_groups, 'player_group');

        $page_title = ! empty($player->ClanTag) ? sprintf('[%s] %s', $player->ClanTag,
            $player->SoldierName) : $player->SoldierName;

        return view('player.profile', compact('player', 'page_title', 'charts', 'isCached', 'groups'));
    }

    /**
     * @param Player $player
     *
     * @return mixed
     * @internal param Player $id
     */
    public function issueForgive(Player $player)
    {
        // Check if current logged in user has the permission to issue forgive points.
        if (! $this->user->ability(null, 'player.infractions.forgive')) {
            return MainHelper::response(null, 'Unauthorized!', 'error', 401);
        }

        // Check for a server id field for which the forgive point to be issued on.
        if (! $this->request->has('server_id')) {
            return MainHelper::response(null, 'Missing server id.', 'error');
        }

        $points = $this->request->get('forgive_points', 1);
        $server_id = $this->request->get('server_id');
        $message = $this->request->get('message', 'ForgivePlayer');
        $response_message = '';

        // Load up the infractions for the player on the selected server.
        $player->load([
            'infractionsServer' => function ($query) use (&$server_id) {
                $query->where('server_id', $server_id);
            },
        ])->first();

        if (empty($player->infractionsServer) || count($player->infractionsServer) == 0) {
            return MainHelper::response(null, sprintf('No infractions found for server #%u', $server_id), 'error', 404);
        }

        // Set the issuing admin name for the record.
        $adminName = $this->user->username;

        // Set the issuing admin id for the record.
        $adminId = null;

        // Check if the issuing admin has a player in the database for the same game as the targeted player.
        $soldier = $this->user->soldiers()->with([
            'player' => function ($query) use (&$player) {
                $query->where('GameID', $player->GameID);
            },
        ])->first();

        // Check if the issuing admin has a player. If they do we need to update the $adminName and $adminId variable.
        if (! is_null($soldier)) {
            $adminName = $soldier->player->SoldierName;
            $adminId = $soldier->player_id;
        }

        $punish_points = $player->infractionsServer[0]->punish_points;
        $forgive_points = $player->infractionsServer[0]->forgive_points;

        if ($forgive_points == $punish_points) {
            return MainHelper::response(null,
                trans('player.admin.forgive.errors.err1', ['player' => $player->SoldierName]));
        }

        if ($points > $punish_points && $forgive_points != $punish_points) {
            // Save the user forgive count into another variable.
            $points_old = $points;

            // Override points with players current punish points.
            $points = $punish_points;

            $response_message = trans('player.admin.forgive.warnings.overage', [
                'player'    => $player->SoldierName,
                'usertotal' => $points_old,
                'reduced'   => $points,
                'remaining' => ($points_old - $points),
            ]);

            MainHelper::response($player, $response_message);
        }

        for ($i = 0; $i < $points; $i++) {
            $record = new Record;
            $record->server_id = $server_id;
            $record->command_type = 10;
            $record->command_type = 10;
            $record->target_name = $player->SoldierName;
            $record->target_id = $player->PlayerID;
            $record->source_name = $adminName;
            $record->source_id = $adminId;
            $record->record_message = $message;
            $record->record_time = Carbon::now();
            $record->adkats_read = 'Y';
            $record->adkats_web = true;
            $record->save();
        }

        return MainHelper::response($player, $response_message);
    }
}
