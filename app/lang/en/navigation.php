<?php

return [
    'main'  => [
        'title' => 'Main Navigation',
        'items' => [
            'dashboard'  => [
                'title' => 'Dashboard',
                'icon'  => [
                    'fa'  => 'fa-dashboard',
                    'ion' => null
                ]
            ],
            'chatlogs'   => [
                'title' => 'Chatlogs',
                'icon'  => [
                    'fa'  => 'fa-comments',
                    'ion' => null
                ]
            ],
            'scoreboard' => [
                'title' => 'Live Scoreboard',
                'icon'  => [
                    'fa'  => 'fa-server',
                    'ion' => null
                ]
            ],
            'playerlist' => [
                'title' => 'Playerlist',
                'icon'  => [
                    'fa'  => 'fa-users',
                    'ion' => null
                ]
            ]
        ]
    ],

    'admin' => [
        'site'   => [
            'title' => 'Site Management',
            'items' => [
                'users' => [
                    'title' => 'Users',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Editing User #:id'
                        ]
                    ]
                ]
            ]
        ],
        'adkats' => [
            'title' => 'AdKats Management',
            'items' => [
                'banlist' => [
                    'title' => 'Banlist',
                    'icon'  => [
                        'fa'  => null,
                        'ion' => 'ion-hammer'
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Editing Ban #:id'
                        ]
                    ]
                ],
                'users'   => [
                    'title' => 'Users',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Editing User #:id'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
