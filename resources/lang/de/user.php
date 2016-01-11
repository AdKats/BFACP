<?php

return [
    'account'       => [
        'settings' => [
            'blocks' => [
                'general'  => [
                    'title'  => 'Allgemein',
                    'inputs' => [
                        'email'    => 'Email',
                        'language' => 'Sprache',
                    ],
                ],
                'password' => [
                    'title'       => 'Passwort ändern',
                    'inputs'      => [
                        'password'         => 'Passwort',
                        'password_confirm' => 'Passwort bestätigen',
                    ],
                    'inputs_help' => [
                        'password'         => 'Um dein Passwort zu ändern gib dein altes Passwort bitte hier ein.',
                        'password_confirm' => 'Um die Passwortänderung zu bestätigen, gib bitte hier dein neues Passwort ein.',
                    ],
                ],
            ],
        ],
    ],
    'notifications' => [
        'account' => [
            'email'    => [
                'changed' => 'Email-Adresse wurde zu :addr! geändert',
            ],
            'password' => [
                'changed' => 'Passwort wurde geändert!',
            ],
            'language' => [
                'changed' => 'Sprache wurde zu :lang! geändert',
            ],
        ],
    ],
];
