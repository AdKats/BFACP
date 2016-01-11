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
                'details' => 'Детали',
                'buttons' => [
                    'save'   => 'Сохранить изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить пользователя',
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
                'details' => 'Детали',
                'inputs'  => [
                    'username' => ['label' => 'Имя'],
                    'email'    => ['label' => 'Email'],
                    'role'     => ['label' => 'Группа'],
                    'lang'     => ['label' => 'Язык'],
                ],
                'buttons' => [
                    'save'   => 'Создать пользователя',
                    'cancel' => 'Отмена',
                ],
            ],
        ],
        'roles'   => [
            'edit'   => [
                'buttons' => [
                    'save'   => 'Сохранить изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить группу',
                ],
            ],
            'create' => [
                'buttons' => [
                    'save'   => 'Создать группу',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить группу',
                ],
            ],
        ],
        'servers' => [
            'edit' => [
                'buttons' => [
                    'save'   => 'Сохранить изменения',
                    'cancel' => 'Отмена',
                    'delete' => 'Удалить сервер',
                ],
            ],
        ],
    ],

];
