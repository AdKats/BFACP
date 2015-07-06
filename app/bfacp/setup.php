<?php

// Check if the storage directory and subfolders are writeable
if (!is_writable(storage_path())) {
    die(sprintf('All folders under %s must be set to 0777.', storage_path()));
}

// Check if the JS Builds folder is writeable
if (!is_writable($jsBuildsPath)) {
    try {
        if (!chmod($jsBuildsPath, 0777)) {
            die(sprintf('Directory "%s" is not writeable. Please change permissions to 0777', $jsBuildsPath));
        }
    } catch (Exception $e) {
        die(sprintf('Directory "%s" is not writeable. Please change permissions to 0777', $jsBuildsPath));
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
