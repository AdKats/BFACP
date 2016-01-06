<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBaseTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the BFACP Settings table
        Schema::create('bfacp_options', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('option_id');
            $table->string('option_key', 64)->unique();
            $table->string('option_title', 100);
            $table->longText('option_value')->nullable();
            $table->string('option_description')->nullable();
        });

        // Creates the users table
        Schema::create('bfacp_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username', 20)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('confirmation_code');
            $table->string('remember_token')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
            $table->timestamp('lastseen_at');
        });

        // Creates the user settings table
        Schema::create('bfacp_settings_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned()->primary();
            $table->string('lang', 3)->default('en')->index();
            $table->string('timezone')->default('UTC')->index();
            $table->boolean('notifications')->default(true);
            $table->boolean('notifications_alert')->default(true);
            $table->string('notifications_alert_sound', 30)->default('alert0');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('bfacp_users')->onUpdate('cascade')->onDelete('cascade');
        });

        // Creates the user soldiers table
        Schema::create('bfacp_users_soldiers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned()->index();
            $table->integer('player_id')->unsigned()->index();
            $table->primary(['user_id', 'player_id']);
            $table->foreign('user_id')->references('id')->on('bfacp_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('player_id')->references('PlayerID')->on('tbl_playerdata')->onUpdate('cascade')->onDelete('cascade');
        });

        // Create servers settings table
        Schema::create('bfacp_settings_servers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->smallInteger('server_id')->unsigned()->primary();
            $table->text('rcon_password')->nullable();
            $table->string('filter')->nullable();
            $table->integer('monitor_key')->unsigned()->nullable();
            $table->string('battlelog_guid', 100)->nullable();
            $table->timestamps();
            $table->foreign('server_id')->references('ServerID')->on('tbl_server')->onUpdate('cascade')->onDelete('cascade');
        });

        // Creates password reminders table
        Schema::create('bfacp_password_reminders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at');
        });

        // Creates the roles table
        Schema::create('bfacp_roles', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Creates the assigned_roles (Many-to-Many relation) table
        Schema::create('bfacp_assigned_roles', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('bfacp_users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('bfacp_roles')->onUpdate('cascade');
        });

        // Creates the permissions table
        Schema::create('bfacp_permissions', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // Creates the permission_role (Many-to-Many relation) table
        Schema::create('bfacp_permission_role', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('bfacp_permissions')->onUpdate('cascade')->onDelete('cascade'); // assumes a users table
            $table->foreign('role_id')->references('id')->on('bfacp_roles')->onUpdate('cascade')->onDelete('cascade');
        });

        // Creates the adkats battlelog players table if it doesn't exist
        if (!Schema::hasTable('adkats_battlelog_players')) {
            Schema::create('adkats_battlelog_players', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->integer('player_id')->unsigned()->primary();
                $table->bigInteger('persona_id')->unsigned()->index();
                $table->bigInteger('user_id')->unsigned()->index();
                $table->string('gravatar', 32)->nullable();
                $table->boolean('persona_banned')->default(false);
                $table->unique(['player_id', 'persona_id']);
                $table->foreign('player_id')->references('PlayerID')->on('tbl_playerdata')->onUpdate('cascade')->onDelete('cascade');
            });
        } // If the table already exists we need to check and make sure it's set to InnoDB.
        else {
            if (Schema::hasTable('adkats_battlelog_players')) {
                $query = DB::table('INFORMATION_SCHEMA.TABLES')
                    ->where('TABLE_SCHEMA', getenv('DB_NAME'))
                    ->where('TABLE_NAME', 'adkats_battlelog_players')
                    ->where('ENGINE', 'MyISAM')
                    ->count();

                if ($query > 0) {
                    Schema::table('adkats_battlelog_players', function (Blueprint $table) {
                        $table->engine = 'InnoDB';
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bfacp_assigned_roles', function (Blueprint $table) {
            $table->dropForeign('bfacp_assigned_roles_user_id_foreign');
            $table->dropForeign('bfacp_assigned_roles_role_id_foreign');
        });

        Schema::table('bfacp_permission_role', function (Blueprint $table) {
            $table->dropForeign('bfacp_permission_role_permission_id_foreign');
            $table->dropForeign('bfacp_permission_role_role_id_foreign');
        });

        Schema::table('bfacp_users_soldiers', function (Blueprint $table) {
            $table->dropForeign('bfacp_users_soldiers_user_id_foreign');
            $table->dropForeign('bfacp_users_soldiers_player_id_foreign');
        });

        Schema::table('bfacp_settings_users', function (Blueprint $table) {
            $table->dropForeign('bfacp_settings_users_user_id_foreign');
        });

        Schema::table('bfacp_settings_servers', function (Blueprint $table) {
            $table->dropForeign('bfacp_settings_servers_server_id_foreign');
        });

        Schema::dropIfExists('bfacp_options');
        Schema::dropIfExists('bfacp_settings_servers');
        Schema::dropIfExists('bfacp_assigned_roles');
        Schema::dropIfExists('bfacp_permission_role');
        Schema::dropIfExists('bfacp_roles');
        Schema::dropIfExists('bfacp_permissions');
        Schema::dropIfExists('bfacp_users_soldiers');
        Schema::dropIfExists('bfacp_settings_users');
        Schema::dropIfExists('bfacp_users');
        Schema::dropIfExists('bfacp_password_reminders');
    }

}
