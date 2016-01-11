<?php

return [
    'main'  => [
        'title' => 'Navigatie',
        'items' => [
            'dashboard'   => [
                'title' => 'Dashboard',
                'icon'  => [
                    'fa'  => 'fa-dashboard',
                    'ion' => null,
                ],
            ],
            'chatlogs'    => [
                'title' => 'Chatlogs',
                'icon'  => [
                    'fa'  => 'fa-comments',
                    'ion' => null,
                ],
            ],
            'scoreboard'  => [
                'title' => 'Live Scorebord',
                'icon'  => [
                    'fa'  => 'fa-server',
                    'ion' => null,
                ],
            ],
            'playerlist'  => [
                'title' => 'Spelerslijst',
                'icon'  => [
                    'fa'  => 'fa-users',
                    'ion' => null,
                ],
            ],
            'maintenance' => [
                'title' => 'Systeem Onderhoud',
                'icon'  => [
                    'fa'  => 'fa-cogs',
                    'ion' => null,
                ],
            ],
        ],
    ],
    'admin' => [
        'site'   => [
            'title' => 'Site Beheer',
            'items' => [
                'users'    => [
                    'title' => 'Gebruikers',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Gebruiker #:id bewerken',
                        ],
                    ],
                ],
                'roles'    => [
                    'title' => 'Rollen',
                    'icon'  => [
                        'fa'  => 'fa-list-ol',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Rol :name bewerken',
                        ],
                        'create' => [
                            'title' => 'Nieuwe Rol',
                        ],
                    ],
                ],
                'settings' => [
                    'title' => 'instellingen',
                    'icon'  => [
                        'fa'  => 'fa-cogs',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'servers'  => [
                    'title' => 'Servers',
                    'icon'  => [
                        'fa'  => 'fa-server',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'updater'  => [
                    'title' => 'Updates',
                    'icon'  => [
                        'fa'  => 'fa-wrench',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'system'   => [
                    'logs' => [
                        'title' => 'Systeem Logs',
                        'icon'  => [
                            'fa'  => 'fa-file',
                            'ion' => null,
                        ],
                    ],
                ],
            ],
        ],
        'adkats' => [
            'title' => 'AdKats Beheer',
            'items' => [

                'banlist'         => [
                    'title' => 'Banlijst',
                    'icon'  => [
                        'fa'  => null,
                        'ion' => 'ion-hammer',
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Ban #:id bewerken',
                        ],
                    ],
                ],
                'users'           => [
                    'title' => 'Gebruikers',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Gebruiker #:id bewerken',
                        ],
                    ],
                ],
                'roles'           => [
                    'title' => 'Rollen',
                    'icon'  => [
                        'fa'  => 'fa-list-ol',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Rol Bewerken',
                        ],
                        'create' => [
                            'title' => 'Nieuwe rol maken',
                        ],
                    ],
                ],
                'special_players' => [
                    'title' => 'Speciale Spelers',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Speciale speler #:id bewerken',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
