<?php

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

class RolePermissionTableSeeder extends Seeder
{
    private $perms = [];

    public function run()
    {
        DB::table('bfadmincp_assigned_roles')->truncate();
        DB::table('bfadmincp_permission_role')->truncate();
        DB::table('bfadmincp_permissions')->truncate();
        DB::table('bfadmincp_roles')->truncate();

        $role_list = [
            ['name' => 'Site Administrator'],
            ['name' => 'Battlefield Admin'],
            ['name' => 'Battlefield Sub-Admin'],
            ['name' => 'Battlefield 3 Admin'],
            ['name' => 'Battlefield 3 Sub-Admin'],
            ['name' => 'Battlefield 4 Admin'],
            ['name' => 'Battlefield 4 Sub-Admin'],
            ['name' => 'Banned'],
            ['name' => 'Registered'],
        ];

        $permission_list = [
            ['name' => 'accessbf3',                 'display_name' => 'Access BF3 Admin'],
            ['name' => 'accessbf4',                 'display_name' => 'Access BF4 Admin'],
            ['name' => 'acp_info_database',         'display_name' => 'View DB Stats'],
            ['name' => 'email_newuser',             'display_name' => 'Receive email on new user signup'],
            ['name' => 'issueforgive',              'display_name' => 'Issue Forgives'],
            ['name' => 'issuepban',                 'display_name' => 'Issue Perma Ban'],
            ['name' => 'issuetban',                 'display_name' => 'Issue Temp Ban'],
            ['name' => 'manage_adkats_bans',        'display_name' => 'View and Manage Bans'],
            ['name' => 'manage_adkats_roles_perms', 'display_name' => 'View and Manage AdKats Roles/Permissions'],
            ['name' => 'manage_adkats_users',       'display_name' => 'View and Manage AdKats Users'],
            ['name' => 'manage_site_roles_perms',   'display_name' => 'View and Manage Site Roles/Permissions'],
            ['name' => 'manage_site_settings',      'display_name' => 'View and Manage Site Settings'],
            ['name' => 'manage_site_users',         'display_name' => 'View and Manage Site Users'],
            ['name' => 'scoreboard.ban',            'display_name' => 'Perma Ban Player'],
            ['name' => 'scoreboard.bf3',            'display_name' => 'Commands Enabled BF3'],
            ['name' => 'scoreboard.bf4',            'display_name' => 'Commands Enabled BF4'],
            ['name' => 'scoreboard.forgive',        'display_name' => 'Forgive Player'],
            ['name' => 'scoreboard.kick',           'display_name' => 'Kick Player'],
            ['name' => 'scoreboard.kickall',        'display_name' => 'Kick All Players'],
            ['name' => 'scoreboard.kill',           'display_name' => 'Kill Player'],
            ['name' => 'scoreboard.nuke',           'display_name' => 'Nuke Server'],
            ['name' => 'scoreboard.pmute',          'display_name' => 'Mute Player'],
            ['name' => 'scoreboard.psay',           'display_name' => 'Send Message Player'],
            ['name' => 'scoreboard.punish',         'display_name' => 'Punish Player'],
            ['name' => 'scoreboard.pyell',          'display_name' => 'Yell Message Player'],
            ['name' => 'scoreboard.say',            'display_name' => 'Send Message Server'],
            ['name' => 'scoreboard.squad',          'display_name' => 'Switch Squad'],
            ['name' => 'scoreboard.tban',           'display_name' => 'Temp Ban Player'],
            ['name' => 'scoreboard.team',           'display_name' => 'Switch Team'],
            ['name' => 'scoreboard.tsay',           'display_name' => 'Send Message Team'],
            ['name' => 'scoreboard.tyell',          'display_name' => 'Yell Message Team'],
            ['name' => 'scoreboard.yell',           'display_name' => 'Yell Message Server'],
            ['name' => 'view_motd',                 'display_name' => 'View Message of the Day'],
            ['name' => 'view_player_guids',         'display_name' => 'View Player EAGUID and PBGUID'],
            ['name' => 'view_player_ip',            'display_name' => 'View Player IP Address'],
            ['name' => 'view_reports',              'display_name' => 'Receive Live Reports'],

        ];

        Permission::insert($permission_list);

        foreach($role_list as $role)
        {
            $temp = array();
            $newrole = new Role;
            $newrole->name = $role['name'];
            $newrole->save();

            switch($role['name'])
            {
                case "Site Administrator":
                    foreach($permission_list as $permission)
                    {
                        $temp[] = $this->_retrivePermId($permission['name']);
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.nuke', 'scoreboard.kickall']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issuepban',
                            'issueforgive',
                            'manage_adkats_bans',
                            'accessbf4',
                            'accessbf3',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield Sub-Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.nuke', 'scoreboard.kickall', 'scoreboard.ban']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issueforgive',
                            'accessbf4',
                            'accessbf3',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield 3 Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.bf4', 'scoreboard.nuke', 'scoreboard.kickall']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issuepban',
                            'issueforgive',
                            'manage_adkats_bans',
                            'accessbf3',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield 3 Sub-Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.bf4', 'scoreboard.nuke', 'scoreboard.kickall', 'scoreboard.ban']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issueforgive',
                            'accessbf3',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield 4 Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.bf3', 'scoreboard.nuke', 'scoreboard.kickall']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issuepban',
                            'issueforgive',
                            'manage_adkats_bans',
                            'accessbf4',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;

                case "Battlefield 4 Sub-Admin":
                    foreach($permission_list as $permission)
                    {
                        if(starts_with($permission['name'], 'scoreboard'))
                        {
                            if(in_array($permission['name'], ['scoreboard.bf3', 'scoreboard.nuke', 'scoreboard.kickall', 'scoreboard.ban']))
                                continue;

                            $temp[] = $this->_retrivePermId($permission['name']);
                        }

                        $allowed = [
                            'issuetban',
                            'issueforgive',
                            'accessbf4',
                            'view_player_guids',
                            'view_player_ip',
                            'view_reports',
                            'view_motd'
                        ];

                        if(in_array($permission['name'], $allowed))
                        {
                            $temp[] = $this->_retrivePermId($permission['name']);
                        }
                    }

                    $newrole->perms()->sync($temp);
                break;
            }
        }

        DB::insert(File::get(storage_path() . '/sql/add_missing_users_role.sql'));
    }

    private function _retrivePermId($permission)
    {
        if(empty($this->perms))
        {
            $perms = Permission::all();

            foreach($perms as $perm)
            {
                $this->perms[$perm->name] = $perm->id;
            }

            return intval($this->perms[$permission]);
        }
        else
        {
            if(array_key_exists($permission, $this->perms))
            {
                return intval($this->perms[$permission]);
            }
        }
    }
}
