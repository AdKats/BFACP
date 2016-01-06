<?php

return [

    'base_url'  => 'admin/site/system/logs',
    'filters'   => [
        'global' => ['before' => 'auth'],
        'view'   => [],
        'delete' => [],
    ],
    'log_dirs'  => ['app' => storage_path() . '/logs'],
    'log_order' => 'desc', // Change to 'desc' for the latest entries first
    'per_page'  => 10,
    'view'      => 'system.logviewer',
    'p_view'    => 'pagination::slider-3',

];
