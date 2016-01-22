<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    private $repository;

    public function run()
    {
        $this->repository = app('BFACP\Repositories\UserRepository');

        $password = 'password';

        $user = $this->repository->signup([
            'username'              => 'Admin',
            'email'                 => 'admin@example.com',
            'password'              => $password,
            'password_confirmation' => $password,
        ], 1, false, true);
    }
}
