<?php

// Only used for local develpment.
// DO NOT USE THIS FILE IN PRODUCTION
if (file_exists($app['path.base'] . '/.env.local.php')) {
    return 'local';
}

if (file_exists($app['path.base'] . '/.env.php')) {
    return 'production';
}

throw new Exception('Invalid environment, check configurations.');
