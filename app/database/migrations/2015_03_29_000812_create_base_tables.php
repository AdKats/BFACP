<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBaseTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Creates the users table
		Schema::create('bfacp_users', function(Blueprint $table) {
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

		// Creates password reminders table
		Schema::create('bfacp_password_reminders', function(Blueprint $table) {
			$table->string('email');
			$table->string('token');
			$table->timestamp('created_at');
		});

		// Creates the roles table
        Schema::create('bfacp_roles', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Creates the assigned_roles (Many-to-Many relation) table
        Schema::create('bfacp_assigned_roles', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('bfacp__users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        // Creates the permissions table
        Schema::create('bfacp_permissions', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // Creates the permission_role (Many-to-Many relation) table
        Schema::create('bfacp_permission_role', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('bfacp_permissions'); // assumes a users table
            $table->foreign('role_id')->references('id')->on('roles');
        });
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

        Schema::dropIfExists('bfacp_assigned_roles');
        Schema::dropIfExists('bfacp_permission_role');
        Schema::dropIfExists('bfacp_roles');
        Schema::dropIfExists('bfacp_permissions');
		Schema::dropIfExists('bfacp_users');
		Schema::dropIfExists('bfacp_password_reminders');
	}

}
