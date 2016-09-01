<?php

use Illuminate\Database\Migrations\Migration;

class AddGooglesecretkeyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('twofactor_auth', 'google2fa_secret')) {
            Schema::table('bfacp_users', function ($table) {
                $table->boolean('twofactor_auth', false);
                $table->string('google2fa_secret', 32)->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
