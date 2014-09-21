<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('bfadmincp_settings'))
		{
			Schema::create('bfadmincp_settings', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('token', 50);
				$table->string('context', 150);
				$table->text('description');
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
		Schema::dropIfExists('bfadmincp_settings');
	}

}
