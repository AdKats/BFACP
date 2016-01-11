<?php namespace BFACP\Events;

use BFACP\Adkats\Ban;
use BFACP\Adkats\Record;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Event as Event;

Event::listen('player.ban', function ($input, $player, $ban = null) {
    // Purge the cache for the player
    $player->forget();

    // If no ban exists for the player we need to create it
    // otherwise we need to fetch it.
    if (is_null($player->ban)) {

        // Init a new ban entry
        $ban = new Ban();

        // Init a new record entry
        $record = new Record();

        // Set the player for the record
        $record->target_id = $player->PlayerID;
        $record->target_name = $player->SoldierName;

        // Associate the player with the ban.
        $ban->player()->associate($player);
    } else {

        if ($ban instanceof Ban) {
            // Don't need too do anything else.
        } else {
            // Retrieve the ban record from the database
            $ban = Ban::findOrFail($ban);
        }

        $oldRecord = $ban->record;

        // Replicate the old record entry
        $record = $ban->record->replicate();

        // Only modify the old record if the command action is a temp or perma ban.
        if (in_array($oldRecord->command_action, [7, 8])) {

            // 72 => Previous Temp Ban
            // 73 => Previous Perm Ban
            $oldRecord->command_action = $oldRecord->command_action == 8 ? 73 : 72;
        }

        // Save any changes made on the old record
        $oldRecord->save();
    }

    // Temp Ban
    if (array_get($input, 'ban_type') == 7) {
        $ban_start = array_get($input, 'ban_start');
        $ban_end = array_get($input, 'ban_end');
        if (!empty($ban_start) && !empty($ban_end)) {

            $startDate = Carbon::parse(array_get($input, 'ban_start'))->setTimezone(new \DateTimeZone('UTC'));
            $endDate = Carbon::parse(array_get($input, 'ban_end'))->setTimezone(new \DateTimeZone('UTC'));

            $ban->ban_startTime = $startDate;
            $ban->ban_endTime = $endDate;
        }
    } // Perma Ban - 20 Years
    elseif (array_get($input, 'ban_type') == 8) {
        $ban->ban_startTime = Carbon::now();
        $ban->ban_endTime = Carbon::parse($ban->ban_startTime)->addYears(20);
    }

    // Stores how long the ban is in minutes
    $ban_duration = $ban->ban_startTime->diffInMinutes($ban->ban_endTime);
    $ban_duration_seconds = $ban->ban_startTime->diffInSeconds($ban->ban_endTime);

    $record->command_type = array_get($input, 'ban_type');
    $record->command_action = array_get($input, 'ban_type');
    $record->command_numeric = $ban_duration;
    $record->server_id = array_get($input, 'ban_server');
    $record->source_id = array_get($input, 'admin_id', null);
    $record->source_name = array_get($input, 'admin_name', Auth::user()->username);
    $record->record_message = array_get($input, 'ban_message');
    $record->record_time = Carbon::now();
    $record->adkats_web = true;
    $record->save();

    // Associate the record with the ban.
    $ban->record()->associate($record);

    // Enforce ban by GUID
    $ban->ban_enforceGUID = array_get($input, 'ban_enforce_guid', 'Y');

    // Enforce ban by Name
    $ban->ban_enforceName = array_get($input, 'ban_enforce_name', 'N');

    // Enforce ban by IP
    $ban->ban_enforceIP = array_get($input, 'ban_enforce_ip', 'N');

    // Update the ban notes if they are different
    if ($ban->ban_notes != array_get($input, 'ban_notes', 'NoNotes')) {
        $ban->ban_notes = array_get($input, 'ban_notes', 'NoNotes');
    }

    // If the ban not in effect we need to re-enable it
    if (is_null($player->ban) || $ban->is_unbanned) {
        $ban->ban_status = 'Active';
    }

    $ban->save();

    return $ban;
});
