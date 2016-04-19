<?php

use BFACP\Account\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'Administrator',
            'Registered',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
