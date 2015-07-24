<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

if (!is_writable(storage_path()) || !is_writable($jsBuildsPath)) {
    try {
        $directorys = [
            storage_path(),
            storage_path() . '/cache',
            storage_path() . '/logs',
            storage_path() . '/meta',
            storage_path() . '/sessions',
            storage_path() . '/views',
            $jsBuildsPath,
        ];

        foreach($directorys as $directory) {
            if (!chmod($directory, 0777)) {
                die(sprintf('Directory "%s" is not writeable. Please change permissions to 0777', storage_path()));
            }
        }
    } catch (Exception $e) {
        die(sprintf('Directory "%s" is not writeable. Please change permissions to 0777', storage_path()));
    }
}

if (version_compare(phpversion(), '5.5.0', '<') || !extension_loaded('mcrypt') || !extension_loaded('pdo')) {
    die(View::make('system.requirements', ['required_php_version' => '5.5.0']));
}

if (Config::get('app.key') == 'YourSecretKey!!!' || empty(Config::get('app.key'))) {
    die('Encryption key not set. Refer to <a href="https://github.com/Prophet731/BFAdminCP/wiki/FAQ#3-could-not-set-encryption-key" target="_blank">FAQ #3</a>');
}

// Check and make sure the sessions table exists otherwise create it.
if (!Schema::hasTable(Config::get('session.table'))) {
    Schema::create(Config::get('session.table'), function ($t) {
        $t->string('id')->unique();
        $t->text('payload');
        $t->integer('last_activity');
    });
}

if (!Schema::hasTable(Config::get('database.migrations'))) {
    define('STDIN', fopen('php://stdin', 'r'));
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
}
