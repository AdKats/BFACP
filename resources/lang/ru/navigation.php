<?php

return [
    'main'  => [
        'title' => 'Навигация',
        'items' => [
            'dashboard'   => [
                'title' => 'Панель Управления',
                'icon'  => [
                    'fa'  => 'fa-dashboard',
                    'ion' => null,
                ],
            ],
            'chatlogs'    => [
                'title' => 'Чат',
                'icon'  => [
                    'fa'  => 'fa-comments',
                    'ion' => null,
                ],
            ],
            'servers' => [
                'title' => 'Серверы',
                'scoreboard'  => [
                    'title' => 'Онлайн Таблица',
                    'icon'  => [
                        'fa'  => 'fa-server',
                        'ion' => null,
                    ],
                ],
                'list'  => [
                    'title' => 'Список',
                    'icon'  => [
                        'fa'  => 'fa-list',
                        'ion' => null,
                    ],
                ]
            ],
            'playerlist'  => [
                'title' => 'Список Игроков',
                'icon'  => [
                    'fa'  => 'fa-users',
                    'ion' => null,
                ],
            ],
            'maintenance' => [
                'title' => 'Системное Обслуживание',
                'icon'  => [
                    'fa'  => 'fa-cogs',
                    'ion' => null,
                ],
            ],
        ],
    ],
    'admin' => [
        'site'   => [
            'title' => 'Управление Сайтом',
            'items' => [
                'users'    => [
                    'title' => 'Пользователи',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit'   => [
                            'title' => 'Редактирование Пользователя #:id',
                        ],
                        'create' => [
                            'title' => 'Создать Нового Пользователя',
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
                            'title' => 'Редактирование Группы :name',
                        ],
                        'create' => [
                            'title' => 'Создание Новой Группы',
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
                    'items' => [
                        'edit' => 'Редактирование :servername',
                    ],
                ],
                'updater'  => [
                    'title' => 'Обновления',
                    'icon'  => [
                        'fa'  => 'fa-wrench',
                        'ion' => null,
                    ],
                    'items' => [],
                ],
                'system'   => [
                    'logs' => [
                        'title' => 'Системный Журнал',
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
                            'title' => 'Редактирование Бана #:id',
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
                            'title' => 'Редактирование Пользователя #:id',
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
                            'title' => 'Редактирование Группы',
                        ],
                        'create' => [
                            'title' => 'Создание Новой Группы',
                        ],
                    ],
                ],
                'special_players' => [
                    'title' => 'Особые Игроки',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Редактирование Особого Игрока #:id',
                        ],
                    ],
                ],
                'infractions' => [
                    'title' => 'Нарушения',
                    'icon'  => [
                        'fa'  => 'fa-users',
                        'ion' => null,
                    ],
                    'items' => [
                        'edit' => [
                            'title' => 'Редактирование Нарушения #:id',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
