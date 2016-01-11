<?php

use BFACP\Account\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'Administrator',
            'Registered',
        ];

        foreach ($roles as $role) {
            $r = new Role;
            $r->name = $role;
            $r->saveOrFail();
        }
    }
}
