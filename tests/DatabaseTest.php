<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseTest extends TestCase
{
    public function testPrerequisiteTables()
    {
        Schema::create('tbl_games', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->tinyInteger('GameID', true, true);
            $table->string('Name', 45)->nullable()->default(null);
        });

        Schema::create('tbl_playerdata', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('PlayerID');
            $table->tinyInteger('GameID', false, true);
            $table->string('ClanTag', 10)->nullable()->default(null);
            $table->string('SoldierName', 30)->nullable()->default(null)->index();
            $table->smallInteger('GlobalRank')->default(0);
            $table->string('PBGUID', 32)->nullable()->default(null)->index();
            $table->string('EAGUID', 35)->nullable()->default(null)->index();
            $table->string('IP_Address', 15)->nullable()->default(null)->index();
            $table->string('CountryCode', 2)->nullable()->default(null)->index();
            $table->unique(['EAGUID', 'GameID'], 'UNIQUE_playerdata');
            $table->foreign('GameID')->references('GameId')->on('tbl_games');
        });

        Schema::create('tbl_server', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->smallIncrements('ServerID');
            $table->tinyInteger('ServerGroup', false, true)->default(0)->index();
            $table->string('IP_Address', 45)->nullable()->default(null);
            $table->string('ServerName', 200)->nullable()->default(null);
            $table->tinyInteger('GameID', false, true);
            $table->smallInteger('usedSlots', false, true)->default(0);
            $table->smallInteger('maxSlots', false, true)->default(0);
            $table->string('mapName', 45)->nullable()->default(null);
            $table->text('fullMapName');
            $table->string('Gamemode', 45)->nullable()->default(null);
            $table->string('GameMod', 45)->nullable()->default(null);
            $table->string('PBversion', 45)->nullable()->default(null);
            $table->string('ConnectionState', 45)->nullable()->default(null);
            $table->unique('IP_Address');
        });

        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * @depends testPrerequisiteTables
     */
    public function testDatabase()
    {
        $this->seeInDatabase('bfacp_roles', ['name' => 'Administrator']);
        $this->seeInDatabase('bfacp_roles', ['name' => 'Registered']);
        $this->seeInDatabase('bfacp_users', ['username' => 'Admin']);
    }
}
