<?php

namespace BFACP\Http\Controllers\Admin\Adkats;

use BFACP\Battlefield\Player as Player;
use BFACP\Battlefield\Server\Server as Server;
use BFACP\Exceptions\MetabansException;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use BFACP\Libraries\Metabans;
use BFACP\Repositories\BanRepository;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Event as Event;

/**
 * Class BansController.
 */
class BansController extends Controller
{
    /**
     * Ban Repository.
     *
     * @var BanRepository
     */
    protected $repository;

    /**
     * Metabans Class.
     *
     * @var Metabans
     */
    protected $metabans = null;

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');

        $this->middleware('permission:admin.adkats.bans.view', [
            'only' => [
                'index',
            ],
        ]);

        $this->middleware('permission:admin.adkats.bans.create', [
            'only' => [
                'create',
            ],
        ]);

        $this->middleware('permission:admin.adkats.bans.edit', [
            'except' => [
                'create',
                'index',
            ],
        ]);

        $this->repository = app(BanRepository::class);

        try {
            $this->metabans = app(Metabans::class);
        } catch (MetabansException $e) {
        }
    }

    /**
     * Shows the ban listing.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->request->has('personal')) {
            $bans = $this->repository->getPersonalBans($this->user->soldiers->pluck('player_id'));
        } else {
            $bans = $this->repository->getBanList();
        }

        $page_title = trans('navigation.admin.adkats.items.banlist.title');

        return view('admin.adkats.bans.index', compact('bans', 'page_title'));
    }

    /**
     * Shows the ban editing page.
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $ban = $this->repository->getBanById($id);
        $servers = Server::where('GameID', $ban->player->GameID)->active()->pluck('ServerName', 'ServerID');

        $page_title = trans('navigation.admin.adkats.items.banlist.items.edit.title', ['id' => $id]);

        return view('admin.adkats.bans.edit', compact('ban', 'servers', 'page_title'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function create()
    {
        $player = Player::findOrFail($this->request->get('player_id'));

        if (! is_null($player->ban)) {
            return redirect()->route('admin.adkats.bans.edit', [$player->ban->ban_id]);
        }

        $servers = Server::where('GameID', $player->game->GameID)->active()->pluck('ServerName', 'ServerID');
        $admin = MainHelper::getAdminPlayer($this->user, $player->game->GameID);

        $page_title = 'Create New Ban';

        return view('admin.adkats.bans.create', compact('player', 'servers', 'admin', 'page_title'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $player = Player::findOrfail($this->request->get('player_id'));

        if (! is_null($player->ban)) {
            return redirect()->route('admin.adkats.bans.edit', [$player->ban->ban_id]);
        }

        $admin = MainHelper::getAdminPlayer($this->user, $player->game->GameID);

        // Save the POST data
        $ban_notes = trim($this->request->get('notes', null));
        $ban_message = trim($this->request->get('message', null));
        $ban_server = $this->request->get('server', null);
        $ban_start = $this->request->get('banStartDateTime', null);
        $ban_end = $this->request->get('banEndDateTime', null);
        $ban_type = $this->request->get('type', null);
        $ban_enforce_guid = (bool) $this->request->get('enforce_guid', false) ? 'Y' : 'N';
        $ban_enforce_name = (bool) $this->request->get('enforce_name', false) ? 'Y' : 'N';
        $ban_enforce_ip = (bool) $this->request->get('enforce_ip', false) ? 'Y' : 'N';

        $admin_id = is_null($admin) ? null : $admin->PlayerID;
        $admin_name = is_null($admin) ? Auth::user()->username : $admin->SoldierName;

        $input = compact('ban_notes', 'ban_message', 'ban_server', 'ban_start', 'ban_end', 'ban_type',
            'ban_enforce_guid', 'ban_enforce_name', 'ban_enforce_ip', 'admin_id', 'admin_name');

        $response = Event::fire('player.ban', [$input, $player])[0];

        $this->messages[] = sprintf('Ban #%u has been created.', $response->ban_id);

        return redirect()->route('admin.adkats.bans.edit', [$response->ban_id])->with('messages', $this->messages);
    }

    /**
     * Updates a existing ban.
     *
     * @param int $id Ban ID
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        // Fetch the ban
        $ban = $this->repository->getBanById($id);

        $admin = MainHelper::getAdminPlayer($this->user, $ban->player->game->GameID);

        // Purge the cache for the player
        $this->cache->forget(sprintf('api.player.%u', $ban->player_id));
        $this->cache->forget(sprintf('player.%u', $ban->player_id));

        // Save the POST data
        $ban_notes = trim($this->request->get('notes', null));
        $ban_message = trim($this->request->get('message', null));
        $ban_server = $this->request->get('server', null);
        $ban_start = $this->request->get('banStartDateTime', null);
        $ban_end = $this->request->get('banEndDateTime', null);
        $ban_type = $this->request->get('type', null);
        $ban_enforce_guid = (bool) $this->request->get('enforce_guid', false);
        $ban_enforce_name = (bool) $this->request->get('enforce_name', false);
        $ban_enforce_ip = (bool) $this->request->get('enforce_ip', false);

        // Temp Ban
        if ($ban_type == 7) {
            if (! empty($ban_start) && ! empty($ban_end)) {
                $startDate = Carbon::parse($ban_start)->setTimezone(new \DateTimeZone('UTC'));
                $endDate = Carbon::parse($ban_end)->setTimezone(new \DateTimeZone('UTC'));

                $ban->ban_startTime = $startDate;
                $ban->ban_endTime = $endDate;
            }
        } // Perma Ban - 20 Years
        elseif ($ban_type == 8) {
            $ban->ban_startTime = Carbon::now();
            $ban->ban_endTime = Carbon::parse($ban->ban_startTime)->addYears(20);
        }

        // If the ban end datetime is passed the current datetime then redirect back and show an error
        if ($ban->ban_endTime->lte(Carbon::now())) {
            return redirect()->route('admin.adkats.bans.edit',
                [$ban->ban_id])->withErrors(['Ban expire date or time cannot be in the past. Cannot update the ban.'])->withInput()->with('messages',
                ['Fields have been preserved with your changes.']);
        }

        // Stores how long the ban is in minutes
        $ban_duration = $ban->ban_startTime->diffInMinutes($ban->ban_endTime);
        $ban_duration_seconds = $ban->ban_startTime->diffInSeconds($ban->ban_endTime);

        $oldRecord = $ban->record;

        // Only update the ban record if the following conditions are met.
        if ($ban_server != $oldRecord->server_id || $ban_message != $oldRecord->record_message || $ban_duration != $oldRecord->command_numeric || $ban_type != $oldRecord->command_action) {

            // If just the server changed do not create a new record just update the old one
            if ($oldRecord->server_id != $ban_server && $ban_message == $oldRecord->record_message && $ban_duration == $oldRecord->command_numeric) {
                // Change the server id for the ban record
                $oldRecord->server_id = $ban_server;
            }

            // If the ban duration is zero, the ban type still set to perm, and the record message didn't change, then only update the command numeric
            // field on the old record to be the correct duration and prevent creating a new record for simple changes.
            else {
                if ($oldRecord->command_numeric == 0 && $ban_type == 8 && $ban_message == $oldRecord->record_message) {
                    $oldRecord->command_numeric = $ban_duration;
                } // Create a new record and update the old record for the ban if the other conditions were not met.
                else {

                    // Only modify the old record if the command action is a temp or perma ban.
                    if (in_array($oldRecord->command_action, [7, 8])) {

                        // 72 => Previous Temp Ban
                        // 73 => Previous Perm Ban
                        $oldRecord->command_action = $oldRecord->command_action == 8 ? 73 : 72;
                    }

                    // If ban reason is the same as the unban reason then prevent the ban update.
                    // Ban reasons should be different from the unban reasons
                    if ($ban_message == $oldRecord->record_message && $oldRecord->command_type == 37) {
                        return redirect()->route('admin.adkats.bans.edit',
                            [$ban->ban_id])->withErrors(['Ban reason is the same as the unban reason. Please change the ban reason.'])->withInput()->with('messages',
                            ['Fields have been preserved with your changes.']);
                    }

                    // Duplicate the record and save the changes
                    $record = $ban->record->replicate();
                    $record->command_type = $ban_type;
                    $record->command_action = $ban_type;
                    $record->command_numeric = $ban_duration;
                    $record->server_id = $ban_server;
                    $record->source_id = is_null($admin) ? null : $admin->PlayerID;
                    $record->source_name = is_null($admin) ? Auth::user()->username : $admin->SoldierName;
                    $record->record_message = $ban_message;
                    $record->record_time = Carbon::now();
                    $record->adkats_web = true;
                    $record->save();

                    // Update the ban record and save the changes
                    $ban->record()->associate($record);

                    try {
                        if (! is_null($this->metabans)) {
                            $this->metabans->assess($ban->player->game->Name, $ban->player->EAGUID, 'Black',
                                $ban_message, $ban_duration_seconds);
                        }
                    } catch (MetabansException $e) {
                    }
                }
            }

            // Save any changes made on the old record
            $oldRecord->save();
        }

        // Update the ban notes if they are different
        if ($ban->ban_notes != $ban_notes) {
            $ban->ban_notes = $ban_notes;
        }

        // If the ban not in effect we need to re-enable it
        if ($ban->is_unbanned) {
            $ban->ban_status = 'Active';
        }

        // Enforce ban by GUID
        $ban->ban_enforceGUID = $ban_enforce_guid ? 'Y' : 'N';

        // Enforce ban by Name
        $ban->ban_enforceName = $ban_enforce_name ? 'Y' : 'N';

        // Enforce ban by IP
        $ban->ban_enforceIP = $ban_enforce_ip ? 'Y' : 'N';

        // Save changes
        $ban->save();

        $this->messages[] = sprintf('Ban #%u has been updated.', $ban->ban_id);

        return redirect()->route('admin.adkats.bans.edit', [$ban->ban_id])->with('messages', $this->messages);
    }

    /**
     * Unban the player.
     *
     * @param int $id Ban ID
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function destroy($id)
    {
        try {
            // Fetch the ban
            $ban = $this->repository->getBanById($id);

            $oldRecord = $ban->record;

            $admin = MainHelper::getAdminPlayer($this->user, $ban->player->game->GameID);

            // Only modify the old record if the command action is a temp or perma ban.
            if (in_array((int) $oldRecord->command_action, [7, 8])) {
                // 72 => Previous Temp Ban
                // 73 => Previous Perm Ban
                $oldRecord->command_action = $oldRecord->command_action == 8 ? 73 : 72;
                $oldRecord->save();
            }

            // Duplicate the record and save the changes
            $record = $ban->record->replicate();
            $record->command_type = 37;
            $record->command_action = 37;
            $record->source_id = is_null($admin) ? null : $admin->PlayerID;
            $record->source_name = is_null($admin) ? Auth::user()->username : $admin->SoldierName;
            $record->record_message = $this->request->get('message', 'Unbanned');
            $record->record_time = Carbon::now();
            $record->adkats_web = true;
            $record->save();

            // Update the ban record and save the changes
            $ban->record()->associate($record);
            $ban->ban_status = 'Disabled';

            if (! is_null($this->request->get('notes', null))) {
                $ban->ban_notes = $this->request->get('notes', 'NoNotes');
            }

            $ban->save();

            try {
                if (! is_null($this->metabans)) {
                    $this->metabans->assess($ban->player->game->Name, $ban->player->EAGUID, 'None',
                        $this->request->get('message', 'Unbanned'));
                }
            } catch (MetabansException $e) {
            }

            // Purge the cache for the player
            $this->cache->forget(sprintf('api.player.%u', $ban->player_id));
            $this->cache->forget(sprintf('player.%u', $ban->player_id));

            return MainHelper::response();
        } catch (ModelNotFoundException $e) {
            return MainHelper::response(null, $e->getMessage(), 'error', 404);
        } catch (\Exception $e) {
            return MainHelper::response(null, $e->getMessage(), 'error', 500);
        }
    }
}
