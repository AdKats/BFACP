<?php

use BFACP\Account\Permission;
use BFACP\Account\Role;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddPusherPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Permission::count() == 0) {
            if (! defined('FIRST_RUN')) {
                define('FIRST_RUN', true);
            }

            return;
        }

        $now = Carbon::now();

        $permissions = [
            [
                'name'         => 'admin.site.pusher.users.view',
                'display_name' => 'Allowed to see online users',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.pusher.chat.view',
                'display_name' => 'Allowed to see site chat',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'name'         => 'admin.site.pusher.chat.talk',
                'display_name' => 'Allowed to send messages on site chat',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        Permission::insert($permissions);

        Role::find(1)->permissions()->sync(Permission::lists('id'));
    }
}
