<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseTest extends TestCase
{
    public function testPrerequisiteTables()
    {
        $sql = File::get(storage_path('sql/tests/PrerequisiteTables.sql'));
        DB::unprepared($sql);
    }

    /**
     * @depends testPrerequisiteTables
     */
    public function testDatabase()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
        $this->seeInDatabase('bfacp_roles', ['name' => 'Administrator']);
        $this->seeInDatabase('bfacp_roles', ['name' => 'Registered']);
        $this->seeInDatabase('bfacp_users', ['username' => 'Admin']);
    }
}
