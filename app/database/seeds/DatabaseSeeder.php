<?php

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->call('OptionsTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('PermissionsTableSeeder');
        $this->call('UserTableSeeder');

        if (defined('FIRST_RUN')) {
            DB::delete("DELETE FROM `bfacp_migrations` WHERE `migration` IN ('2016_01_02_173641_add_russian_language', '2016_01_06_002532_add_pusher_permission')");
            Artisan::call('migrate', ['--force' => true]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
