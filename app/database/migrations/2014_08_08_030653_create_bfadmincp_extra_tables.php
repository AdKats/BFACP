<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBfadmincpExtraTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if(!Schema::hasTable('bfadmincp_battlelog_playerdata'))
        {
            Schema::create('bfadmincp_battlelog_playerdata', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->increments('id');
                $table->integer('player_id')->unsigned();
                $table->integer('persona_id')->unsigned();

                $table->unique(array('player_id', 'persona_id'));

                $table->foreign('player_id')
                      ->references('PlayerID')
                      ->on('tbl_playerdata')
                      ->onDelete('cascade')
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
		Schema::table('bfadmincp_battlelog_playerdata', function(Blueprint $table)
        {
            $table->dropForeign('bfadmincp_battlelog_playerdata_player_id_foreign');
        });

        Schema::dropIfExists('bfadmincp_battlelog_playerdata');
	}

}
