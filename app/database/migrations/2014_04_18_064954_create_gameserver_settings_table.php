<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameserverSettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('bfadmincp_settings_gameserver'))
        {
            Schema::create('bfadmincp_settings_gameserver', function(Blueprint $table)
            {
                // Set the table engine
                $table->engine = 'InnoDB';

                // Define the fields
                $table->smallInteger('server_id')->unsigned()->primary();
                $table->text('rcon_pass_hash')->nullable()->default(NULL);
                $table->text('name_strip')->nullable()->default(NULL);
                $table->integer('uptime_robot_id')->nullable()->unsigned()->default(NULL);
                $table->timestamps();

                $table->foreign('server_id')
                      ->references('ServerID')
                      ->on('tbl_server')
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
        Schema::table('bfadmincp_settings_gameserver', function(Blueprint $table)
        {
            $table->dropForeign('bfadmincp_settings_gameserver_server_id_foreign');
        });

        Schema::dropIfExists('bfadmincp_settings_gameserver');
    }

}
