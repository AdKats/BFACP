<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DatabaseTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    public function testDatabase()
    {
        $this->seeInDatabase('bfacp_roles', ['name' => 'Administrator']);
        $this->seeInDatabase('bfacp_roles', ['name' => 'Registered']);
        $this->seeInDatabase('bfacp_users', ['username' => 'Admin']);
    }
}
