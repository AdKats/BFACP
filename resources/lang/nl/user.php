<?php

return [
    'account'       => [
        'settings' => [
            'blocks' => [
                'general'  => [
                    'title'  => 'Algemeen',
                    'inputs' => [
                        'email'    => 'Email',
                        'language' => 'Language',
                    ],
                ],
                'password' => [
                    'title'       => 'Verander wachtwoord',
                    'inputs'      => [
                        'password'         => 'Wachtwoord',
                        'password_confirm' => 'bevestig wachtwoord',
                    ],
                    'inputs_help' => [
                        'password'         => 'Om uw wachtwoord te wijzigen kunt u dat hier invoeren.',
                        'password_confirm' => 'Typ uw nieuwe wachtwoord in om de wijziging te bevestigen.',
                    ],
                ],
            ],
        ],
    ],
    'notifications' => [
        'account' => [
            'email'    => [
                'changed' => 'E-mail is bijgewerkt naar :addr!',
            ],
            'password' => [
                'changed' => 'Wachtwoord is veranderd!',
            ],
            'language' => [
                'changed' => 'Taal is gewijzigd in:lang!',
            ],
        ],
    ],
];
