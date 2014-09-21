<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRolesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*=========================================================
        =            Create Confide and Entrust Tables            =
        =========================================================*/

        if(!Schema::hasTable('bfadmincp_users'))
        {
            Schema::create('bfadmincp_users', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id');
                $table->string('username', 20);
                $table->string('email');
                $table->string('password');
                $table->string('confirmation_code');
                $table->boolean('confirmed')->default(false);
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
                $table->timestamp('lastseen_at')->default('0000-00-00 00:00:00');
            });
        }

        if(!Schema::hasTable('bfadmincp_password_reminders'))
        {
            Schema::create('bfadmincp_password_reminders', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->string('email');
                $table->string('token');
                $table->timestamp('created_at');
            });
        }

        if(!Schema::hasTable('bfadmincp_roles'))
        {
            Schema::create('bfadmincp_roles', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id')->unsigned();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('bfadmincp_assigned_roles'))
        {
            Schema::create('bfadmincp_assigned_roles', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->integer('role_id')->unsigned();

                // Define the foreign keys
                $table->foreign('user_id')->references('id')->on('bfadmincp_users');
                $table->foreign('role_id')->references('id')->on('bfadmincp_roles');
            });
        }

        if(!Schema::hasTable('bfadmincp_permissions'))
        {
            Schema::create('bfadmincp_permissions', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id')->unsigned();
                $table->string('name');
                $table->string('display_name');
            });
        }

        if(!Schema::hasTable('bfadmincp_permission_role'))
        {
            Schema::create('bfadmincp_permission_role', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id')->unsigned();
                $table->integer('permission_id')->unsigned();
                $table->integer('role_id')->unsigned();

                // Define the foreign keys
                $table->foreign('permission_id')->references('id')->on('bfadmincp_permissions'); // assumes a users table
                $table->foreign('role_id')->references('id')->on('bfadmincp_roles');
            });
        }

        /*-----  End of Create Confide and Entrust Tables  ------*/

        if(!Schema::hasTable('bfadmincp_user_preferences'))
        {
            Schema::create('bfadmincp_user_preferences', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->integer('user_id')->unsigned()->primary();
                $table->string('lang', 2)->default('en');
                $table->string('gravatar', 100)->nullable()->default(NULL);
                $table->string('timezone')->default('UTC');
                $table->boolean('report_notify_alert')->default(true);
                $table->boolean('report_notify_sound')->default(true);
                $table->string('report_notify_sound_file')->default('alert00');
                $table->integer('bf3_playerid')->nullable()->unsigned()->default(NULL)->index();
                $table->integer('bf4_playerid')->nullable()->unsigned()->default(NULL)->index();
                $table->timestamps();

                // Define foreign keys

                $table->foreign('user_id')
                      ->references('id')
                      ->on('bfadmincp_users')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');

                $table->foreign('bf3_playerid')
                      ->references('PlayerID')
                      ->on('tbl_playerdata')
                      ->onDelete('set null')
                      ->onUpdate('cascade');

                $table->foreign('bf4_playerid')
                      ->references('PlayerID')
                      ->on('tbl_playerdata')
                      ->onDelete('set null')
                      ->onUpdate('cascade');
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
        Schema::table('bfadmincp_assigned_roles', function(Blueprint $table)
        {
            $table->dropForeign('bfadmincp_assigned_roles_user_id_foreign');
            $table->dropForeign('bfadmincp_assigned_roles_role_id_foreign');
        });

        Schema::table('bfadmincp_permission_role', function(Blueprint $table)
        {
            $table->dropForeign('bfadmincp_permission_role_permission_id_foreign');
            $table->dropForeign('bfadmincp_permission_role_role_id_foreign');
        });

        Schema::table('bfadmincp_user_preferences', function(Blueprint $table)
        {
            $table->dropForeign('bfadmincp_user_preferences_user_id_foreign');
            $table->dropForeign('bfadmincp_user_preferences_bf3_playerid_foreign');
            $table->dropForeign('bfadmincp_user_preferences_bf4_playerid_foreign');
        });

        Schema::dropIfExists('bfadmincp_assigned_roles');
        Schema::dropIfExists('bfadmincp_permission_role');
        Schema::dropIfExists('bfadmincp_roles');
        Schema::dropIfExists('bfadmincp_permissions');
        Schema::dropIfExists('bfadmincp_user_preferences');
        Schema::dropIfExists('bfadmincp_password_reminders');
        Schema::dropIfExists('bfadmincp_users');
    }
}
