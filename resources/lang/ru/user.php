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
                    'title'       => 'Изменить Пароль',
                    'inputs'      => [
                        'password'         => 'Пароль',
                        'password_confirm' => 'Повторите Пароль',
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
                'changed' => 'Пароль был изменен!',
            ],
            'language' => [
                'changed' => 'Язык был изменен на :lang!',
            ],
        ],
    ],
];
