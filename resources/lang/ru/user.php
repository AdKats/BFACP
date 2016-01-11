<?php

return [
    'account'       => [
        'settings' => [
            'blocks' => [
                'general'  => [
                    'title'  => 'Общее',
                    'inputs' => [
                        'email'    => 'Email',
                        'language' => 'Язык',
                    ],
                ],
                'password' => [
                    'title'       => 'Изменить пароль',
                    'inputs'      => [
                        'password'         => 'Пароль',
                        'password_confirm' => 'Повторите пароль',
                    ],
                    'inputs_help' => [
                        'password'         => 'Чтобы изменить пароль, введите его здесь.',
                        'password_confirm' => 'Введите пароль заного, чтобы применить изменения.',
                    ],
                ],
            ],
        ],
    ],
    'notifications' => [
        'account' => [
            'email'    => [
                'changed' => 'Email был изменен на :addr!',
            ],
            'password' => [
                'changed' => 'Пароль изменен!',
            ],
            'language' => [
                'changed' => 'Язык был изменен на :lang!',
            ],
        ],
    ],
];
