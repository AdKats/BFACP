<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
 */

foreach (glob(app_path() . '/bfacp/Commands/*.php') as $file) {
    if (preg_match("/([a-zA-Z]+\.php)/", $file, $matches)) {
        $classname = str_replace('.php', null, $matches[1]);
        Artisan::resolve(sprintf('BFACP\Commands\%s', $classname));
    }
}
