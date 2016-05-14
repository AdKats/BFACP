<?php

if (! is_writable(storage_path()) || ! is_writable($jsBuildsPath)) {
    $directories = [
        storage_path(),
        storage_path().'/cache',
        storage_path().'/logs',
        storage_path().'/meta',
        storage_path().'/sessions',
        storage_path().'/views',
        $jsBuildsPath,
    ];

    foreach ($directories as $directory) {
        try {
            chmod($directory, 0777);
        } catch (Exception $e) {
            die(sprintf('Directory "%s" is not writable. Please change permissions to 0777.', $directory));
        }
    }
}

if (Config::get('app.key') == 'SomeRandomString' || Config::get('app.key') === '') {
    die('Encryption key not set. Refer to <a href="https://github.com/Prophet731/BFAdminCP/wiki/FAQ#3-could-not-set-encryption-key" target="_blank">FAQ #3</a>');
}

if (! Schema::hasTable(Config::get('database.migrations'))) {
    if (! defined('STDIN')) {
        define('STDIN', fopen('php://stdin', 'r'));
    }

    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
}
