<?php

use BFACP\Account\Permission;
use BFACP\Account\Role;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $permissions = [
            [
                'name'         => 'admin.adkats.bans.create',
                'display_name' => 'Create New Bans',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.bans.edit',
                'display_name' => 'Edit Bans',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.bans.view',
                'display_name' => 'View Banlist',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.reports.edit',
                'display_name' => 'Edit Admin Reports',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.reports.view',
                'display_name' => 'View Admin Reports',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.settings.edit',
                'display_name' => 'Edit AdKats Settings',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.special.edit',
                'display_name' => 'Edit AdKats Special Players',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.special.view',
                'display_name' => 'View AdKats Special Players',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.user.edit',
                'display_name' => 'Edit AdKats Users',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.user.view',
                'display_name' => 'View AdKats Users',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.roles.edit',
                'display_name' => 'Edit AdKats Roles',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.adkats.roles.view',
                'display_name' => 'View AdKats Roles',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.forgive',
                'display_name' => 'Forgive Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.kick',
                'display_name' => 'Kick Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.kickall',
                'display_name' => 'Kick All Players',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.kill',
                'display_name' => 'Kill Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.mute',
                'display_name' => 'Mute Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.nuke',
                'display_name' => 'Nuke Server',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.pban',
                'display_name' => 'Perma Ban Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.punish',
                'display_name' => 'Punish Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.say',
                'display_name' => 'Say Message',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.tban',
                'display_name' => 'Temp Ban Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.teamswitch',
                'display_name' => 'Teamswitch Player',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.yell',
                'display_name' => 'Yell Message',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.scoreboard.tell',
                'display_name' => 'Tell Message (Sends both Say and Yell)',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.motd',
                'display_name' => 'View Message of the Day',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.roles',
                'display_name' => 'Manage Site Roles',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.settings.server',
                'display_name' => 'Manage Server Settings',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.settings.site',
                'display_name' => 'Manage Site Settings',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.users',
                'display_name' => 'Manage Users',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.users.notify',
                'display_name' => 'Receive emails on new user registration',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.system.logs',
                'display_name' => 'View the application logs',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.player.special.add',
                'display_name' => 'Add player to the special players table',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.player.special.remove',
                'display_name' => 'Remove player from the special players table',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'chatlogs',
                'display_name' => 'View Chatlogs',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'player.infractions.forgive',
                'display_name' => 'Issue Forgive Points',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'player.view.guids',
                'display_name' => 'View Player GUIDS',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'player.view.ip',
                'display_name' => 'View Player IP',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        Permission::insert($permissions);

        Role::find(1)->permissions()->attach(Permission::lists('id'));
    }
}
