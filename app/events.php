<?php

Event::listen('ban.update.expired', function()
{
    $updated = DB::update("UPDATE `adkats_bans` SET `ban_status` = 'Expired' WHERE `ban_endTime` <= UTC_TIMESTAMP() AND `ban_status` = 'Active'");

    return intval($updated);
});

Event::listen('cleanup_infractions', function()
{
    $deleted = 0;

    $deleted += DB::delete("DELETE FROM `adkats_infractions_server` WHERE punish_points = 0 AND forgive_points = 0 AND total_points = 0");
    $deleted += DB::delete("DELETE FROM `adkats_infractions_global` WHERE punish_points = 0 AND forgive_points = 0 AND total_points = 0");

    return intval($deleted);
});

Event::listen('fix_negative_infractions', function()
{
    $infractions_server = DB::table('adkats_infractions_server')->where('total_points', '<', 0)->get();

    $updated = 0;

    foreach($infractions_server as $infraction)
    {
        $real_total = abs($infraction->total_points);

        if($infraction->forgive_points > 0 && $infraction->punish_points == 0)
            $real_total = $infraction->forgive_points;

        $updated += DB::delete("DELETE FROM `adkats_records_main` WHERE `target_id` = :pid AND `command_type` = 10 AND `server_id` = :sid LIMIT " . $real_total, [
            'pid' => $infraction->player_id,
            'sid' => $infraction->server_id
        ]);
    }

    return $updated;
});

Event::listen('admin.user.create', function($user)
{
    $admins = User::select('bfadmincp_users.*')->join('bfadmincp_assigned_roles', 'bfadmincp_users.id', '=', 'bfadmincp_assigned_roles.user_id')
                ->join('bfadmincp_permission_role', 'bfadmincp_assigned_roles.role_id', '=', 'bfadmincp_permission_role.role_id')
                ->join('bfadmincp_permissions', 'bfadmincp_permission_role.permission_id', '=', 'bfadmincp_permissions.id')
                ->where('bfadmincp_permissions.name', 'email_newuser')->get();

    foreach($admins as $admin)
    {
        Mail::send('emails.admin.newuser', ['admin' => $admin, 'user' => $user], function($message) use(&$admin)
        {
            $message->to($admin->email, $admin->username)->subject('New User Signup');
        });
    }
});

Event::listen('user.lastseen', function($user)
{
    $user->lastseen_at = Carbon::now();

    $user->save();
});
