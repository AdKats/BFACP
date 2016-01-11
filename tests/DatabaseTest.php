<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function testDatabase()
    {
        $sql = File::get(storage_path('sql/tests/PrerequisiteTables.sql'));

        DB::statement($sql);

        Artisan::call('db:migrate');
        Artisan::call('db:seed');
        $this->seeInDatabase('bfacp_roles', ['name' => 'Administrator']);
        $this->seeInDatabase('bfacp_roles', ['name' => 'Registered']);
        $this->seeInDatabase('bfacp_users', ['username' => 'Admin']);
    }
}
