<?php

return [

    'admin' => [
        'users'   => [
            'listing' => [
                'table'  => [
                    'col1' => 'Имя',
                    'col2' => 'Email',
                    'col3' => 'Группа',
                    'col4' => 'Язык',
                    'col5' => 'Статус',
                    'col6' => 'Создан',
                ],
                'status' => [
                    'active'   => 'Включен',
                    'inactive' => 'Выключен',
                ],
            ],
            'edit'    => [
                'details' => 'Подробности',
                'buttons' => [
                    'save'   => 'Сохранить Изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить Пользователя',
                ],
                'inputs'  => [
                    'username'       => ['label' => 'Имя'],
                    'email'          => ['label' => 'Email'],
                    'role'           => ['label' => 'Группа'],
                    'account_status' => ['label' => 'Статус'],
                    'lang'           => ['label' => 'Язык'],
                    'genpass'        => ['label' => 'Сгенерировать новый пароль'],
                ],
            ],
            'updates' => [
                'password' => [
                    'generated' => 'Пользователю :username (:email) был отправлен новый пароль.',
                ],
            ],
            'create'  => [
                'details' => 'Подробности',
                'inputs'  => [
                    'username' => ['label' => 'Имя'],
                    'email'    => ['label' => 'Email'],
                    'role'     => ['label' => 'Группа'],
                    'lang'     => ['label' => 'Язык'],
                ],
                'buttons' => [
                    'save'   => 'Создать Пользователя',
                    'cancel' => 'Отмена',
                ],
            ],
        ],
        'roles'   => [
            'edit'   => [
                'buttons' => [
                    'save'   => 'Сохранить Изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить Группу',
                ],
            ],
            'create' => [
                'buttons' => [
                    'save'   => 'Создать Группу',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить Группу',
                ],
            ],
        ],
        'servers' => [
            'edit' => [
                'buttons' => [
                    'save'   => 'Сохранить Изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить Сервер',
                ],
            ],
        ],
    ],

];
