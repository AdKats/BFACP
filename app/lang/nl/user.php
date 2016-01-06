<?php

return [
    'account'       => [
        'settings' => [
            'blocks' => [
                'general'  => [
                    'title'  => 'General',
                    'inputs' => [
                        'email'    => 'Email',
                        'language' => 'Language',
                    ],
                ],
                'password' => [
                    'title'       => 'Change Password',
                    'inputs'      => [
                        'password'         => 'Password',
                        'password_confirm' => 'Confirm Password',
                    ],
                    'inputs_help' => [
                        'password'         => 'To change your password please enter it here.',
                        'password_confirm' => 'Retype your new password to confirm the change.',
                    ],
                ],
            ],
        ],
    ],
    'notifications' => [
        'account' => [
            'email'    => [
                'changed' => 'Email has been updated to :addr!',
            ],
            'password' => [
                'changed' => 'Password has been changed!',
            ],
            'language' => [
                'changed' => 'Language has been changed to :lang!',
            ],
        ],
    ],
];
