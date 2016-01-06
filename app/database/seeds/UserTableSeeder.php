<?php

class UserTableSeeder extends Seeder
{
    private $repository;

    public function run()
    {
        $this->repository = App::make('BFACP\Repositories\UserRepository');

        $password = 'password';

        $user = $this->repository->signup([
            'username'              => 'Admin',
            'email'                 => 'admin@example.com',
            'password'              => $password,
            'password_confirmation' => $password,
        ], 1, true, false, true);
    }
}
