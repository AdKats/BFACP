<?php

return [
    'main'  => [
        'title' => 'Навигация',
        'items' => [
            'dashboard'   => [
                'title' => 'Панель управления',
                'icon'  => [
                    'fa'  => 'fa-dashboard',
                    'ion' => null,
                ],
            ],
            'chatlogs'    => [
                'title' => 'Лог чата',
                'icon'  => [
                    'fa'  => 'fa-comments',
                    'ion' => null,
                ],
            ],
            'scoreboard'  => [
                'title' => 'Онлайн таблица',
                'icon'  => [
                    'fa'  => 'fa-server',
                    'ion' => null,
                ],
            ],
            'playerlist'  => [
                'title' => 'Список игроков',
                'icon'  => [
                    'fa'  => 'fa-users',
                    'ion' => null,
                ],
            ],
            'maintenance' => [
                'title' => 'Обслуживание',
                'icon'  => [
                    'fa'  => 'fa-cogs',
                    'ion' => null,
                ],
            ],
        ],
    ],
    'admin' => [
        'site'   => [
            'title' => 'Управление сайтом',
            'items' => [
                'users'    => [
                    'title' => 'Пользователи',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Редактирование пользователя #:id',
                        ],
                        'create' => [
                            'title' => 'Создать нового пользователя',
                        ],
                    ],
                ],
                'roles'    => [
                    'title' => 'Группы',
                    'icon'  => [
                        'fa'  => 'fa-list-ol',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Редактирование :name группы',
                        ],
                        'create' => [
                            'title' => 'Создание новой группы',
                        ],
                    ],
                ],
                'settings' => [
                    'title' => 'Настройки',
                    'icon'  => [
                        'fa'  => 'fa-cogs',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'servers'  => [
                    'title' => 'Серверы',
                    'icon'  => [
                        'fa'  => 'fa-server',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'updater'  => [
                    'title' => 'Обновление',
                    'icon'  => [
                        'fa'  => 'fa-wrench',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'system'   => [
                    'logs' => [
                        'title' => 'Системные логи',
                        'icon'  => [
                            'fa'  => 'fa-file',
                            'ion' => null,
                        ],
                    ],
                ],
            ],
        ],
        'adkats' => [
            'title' => 'Управление AdKats',
            'items' => [

                'banlist'         => [
                    'title' => 'Банлист',
                    'icon'  => [
                        'fa'  => null,
                        'ion' => 'ion-hammer',
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Редактирование бана #:id',
                        ],
                    ],
                ],
                'users'           => [
                    'title' => 'Пользователи',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Редактирование пользователя #:id',
                        ],
                    ],
                ],
                'roles'           => [
                    'title' => 'Группы',
                    'icon'  => [
                        'fa'  => 'fa-list-ol',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Редактирование группы',
                        ],
                        'create' => [
                            'title' => 'Создание новой группы',
                        ],
                    ],
                ],
                'special_players' => [
                    'title' => 'Особые игроки',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Редактирование особого игрока #:id',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
