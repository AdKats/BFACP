<?php namespace BFACP\Http\Controllers\Admin\AdKats;

use BFACP\Battlefield\Player as Player;
use BFACP\Battlefield\Server\Server as Server;
use BFACP\Exceptions\MetabansException;
use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use BFACP\Libraries\Metabans;
use BFACP\Repositories\BanRepository;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Event as Event;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Lang as Lang;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\View as View;

class BansController extends BaseController
{
    /**
     * Ban Repository
     *
     * @var BanRepository
     */
    protected $repository;

    /**
     * Metabans Class
     *
     * @var Metabans
     */
    protected $metabans = null;

    public function __construct()
    {
        parent::__construct();

        $this->repository = App::make('BFACP\Repositories\BanRepository');

        try {
            $this->metabans = App::make('BFACP\Libraries\Metabans');
        } catch (MetabansException $e) {
        }
    }

    /**
     * Shows the ban listing
     */
    public function index()
    {
        if (Input::has('personal')) {
            $bans = $this->repository->getPersonalBans($this->user->soldiers->lists('player_id'));
        } else {
            $bans = $this->repository->getBanList();
        }

        return View::make('admin.adkats.bans.index', compact('bans'))->with('page_title',
            Lang::get('navigation.admin.adkats.items.banlist.title'));
    }

    /**
     * Shows the ban editing page
     *
     * @param  integer $id Ban ID
     */
    public function edit($id)
    {
        try {

            $ban = $this->repository->getBanById($id);
            $servers = Server::where('GameID', $ban->player->GameID)->active()->lists('ServerName', 'ServerID');

            return View::make('admin.adkats.bans.edit', compact('ban', 'servers'))->with('page_title',
                Lang::get('navigation.admin.adkats.items.banlist.items.edit.title', ['id' => $id]));
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.bans.index')->withErrors([sprintf('Ban #%u doesn\'t exist.', $id)]);
        }
    }

    public function create()
    {
        try {

            $player = Player::findOrFail(Input::get('player_id'));

            if (!is_null($player->ban)) {
                return Redirect::route('admin.adkats.bans.edit', [$player->ban->ban_id]);
            }

            $servers = Server::where('GameID', $player->game->GameID)->active()->lists('ServerName', 'ServerID');
            $bfacp = App::make('bfadmincp');
            $admin = MainHelper::getAdminPlayer($bfacp->user, $player->game->GameID);

            return View::make('admin.adkats.bans.create', compact('player', 'servers', 'admin'))->with('page_title',
                'Create New Ban');
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.bans.index')->withErrors([
                sprintf('Player #%u doesn\'t exist.', Input::get('player_id')),
            ]);
        }
    }

    public function store()
    {
        try {
            $player = Player::findOrfail(Input::get('player_id'));

            if (!is_null($player->ban)) {
                return Redirect::route('admin.adkats.bans.edit', [$player->ban->ban_id]);
            }

            $bfacp = App::make('bfadmincp');
            $admin = MainHelper::getAdminPlayer($bfacp->user, $player->game->GameID);

            // Save the POST data
            $ban_notes = trim(Input::get('notes', null));
            $ban_message = trim(Input::get('message', null));
            $ban_server = Input::get('server', null);
            $ban_start = Input::get('banStartDateTime', null);
            $ban_end = Input::get('banEndDateTime', null);
            $ban_type = Input::get('type', null);
            $ban_enforce_guid = (bool)Input::get('enforce_guid', false) ? 'Y' : 'N';
            $ban_enforce_name = (bool)Input::get('enforce_name', false) ? 'Y' : 'N';
            $ban_enforce_ip = (bool)Input::get('enforce_ip', false) ? 'Y' : 'N';

            $admin_id = is_null($admin) ? null : $admin->PlayerID;
            $admin_name = is_null($admin) ? Auth::user()->username : $admin->SoldierName;

            $input = compact('ban_notes', 'ban_message', 'ban_server', 'ban_start', 'ban_end', 'ban_type',
                'ban_enforce_guid', 'ban_enforce_name', 'ban_enforce_ip', 'admin_id', 'admin_name');

            $response = Event::fire('player.ban', [$input, $player])[0];

            $this->messages[] = sprintf('Ban #%u has been created.', $response->ban_id);

            return Redirect::route('admin.adkats.bans.edit', [$response->ban_id])->with('messages', $this->messages);
        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.bans.index')->withErrors([
                sprintf('Player #%u doesn\'t exist.', Input::get('player_id')),
            ]);
        }
    }

    /**
     * Updates a existing ban
     *
     * @param  integer $id Ban ID
     */
    public function update($id)
    {
        try {
            // Fetch the ban
            $ban = $this->repository->getBanById($id);

            $bfacp = App::make('bfadmincp');
            $admin = MainHelper::getAdminPlayer($bfacp->user, $ban->player->game->GameID);

            // Purge the cache for the player
            Cache::forget(sprintf('api.player.%u', $ban->player_id));
            Cache::forget(sprintf('player.%u', $ban->player_id));

            // Save the POST data
            $ban_notes = trim(Input::get('notes', null));
            $ban_message = trim(Input::get('message', null));
            $ban_server = Input::get('server', null);
            $ban_start = Input::get('banStartDateTime', null);
            $ban_end = Input::get('banEndDateTime', null);
            $ban_type = Input::get('type', null);
            $ban_enforce_guid = (bool)Input::get('enforce_guid', false);
            $ban_enforce_name = (bool)Input::get('enforce_name', false);
            $ban_enforce_ip = (bool)Input::get('enforce_ip', false);

            // Temp Ban
            if ($ban_type == 7) {
                if (!empty($ban_start) && !empty($ban_end)) {

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
                return Redirect::route('admin.adkats.bans.edit',
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
                            return Redirect::route('admin.adkats.bans.edit',
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
                            if (!is_null($this->metabans)) {
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

            // If the ban not in effect we need to renable it
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

            return Redirect::route('admin.adkats.bans.edit', [$ban->ban_id])->with('messages', $this->messages);

        } catch (ModelNotFoundException $e) {
            return Redirect::route('admin.adkats.bans.index')->withErrors([sprintf('Ban #%u doesn\'t exist.', $id)]);
        }
    }

    /**
     * Unbans the player
     *
     * @param  integer $id Ban ID
     *
     * @return \Illuminate\Support\Facades\Response
     */
    public function destroy($id)
    {
        try {
            // Fetch the ban
            $ban = $this->repository->getBanById($id);

            $bfacp = App::make('bfadmincp');

            $oldRecord = $ban->record;

            $admin = MainHelper::getAdminPlayer($bfacp->user, $ban->player->game->GameID);

            // Only modify the old record if the command action is a temp or perma ban.
            if (in_array((int)$oldRecord->command_action, [7, 8])) {
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
            $record->record_message = Input::get('message', 'Unbanned');
            $record->record_time = Carbon::now();
            $record->adkats_web = true;
            $record->save();

            // Update the ban record and save the changes
            $ban->record()->associate($record);
            $ban->ban_status = 'Disabled';

            if (!is_null(Input::get('notes', null))) {
                $ban->ban_notes = Input::get('notes', 'NoNotes');
            }

            $ban->save();

            try {
                if (!is_null($this->metabans)) {
                    $this->metabans->assess($ban->player->game->Name, $ban->player->EAGUID, 'None',
                        Input::get('message', 'Unbanned'));
                }
            } catch (MetabansException $e) {
            }

            // Purge the cache for the player
            Cache::forget(sprintf('api.player.%u', $ban->player_id));
            Cache::forget(sprintf('player.%u', $ban->player_id));

            return MainHelper::response();
        } catch (ModelNotFoundException $e) {
            return MainHelper::response(null, $e->getMessage(), 'error', 404);
        } catch (\Exception $e) {
            return MainHelper::response(null, $e->getMessage(), 'error', 500);
        }
    }
}
