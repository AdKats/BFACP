<?php

return [

    'admin' => [
        'users'   => [
            'listing' => [
                'table'  => [
                    'col1' => 'Benutzername',
                    'col2' => 'Email',
                    'col3' => 'Rolle',
                    'col4' => 'Sprache',
                    'col5' => 'Status',
                    'col6' => 'Erstellt',
                ],
                'status' => [
                    'active'   => 'Aktiv',
                    'inactive' => 'Inaktiv',
                ],
            ],
            'edit'    => [
                'details' => 'Details',
                'buttons' => [
                    'save'   => 'Änderungen speichern',
                    'cancel' => 'Abbrechen',
                    'delete' => 'Benutzer löschen',
                ],
                'inputs'  => [
                    'username'       => ['label' => 'Benutzername'],
                    'email'          => ['label' => 'Email'],
                    'role'           => ['label' => 'Rolle'],
                    'account_status' => ['label' => 'Status'],
                    'lang'           => ['label' => 'Sprache'],
                    'genpass'        => ['label' => 'Generiere neues Passwort für den Benutzer'],
                ],
            ],
            'updates' => [
                'password' => [
                    'generated' => ':username (:email) wurde sein neues Passwort gesendet.',
                ],
            ],
        ],
        'roles'   => [
            'edit'   => [
                'buttons' => [
                    'save'   => 'Änderungen speichern',
                    'cancel' => 'Abbrechen',
                    'delete' => 'Rolle löschen',
                ],
            ],
            'create' => [
                'buttons' => [
                    'save'   => 'Rolle erstellen',
                    'cancel' => 'Abbrechen',
                    'delete' => 'Rolle löschen',
                ],
            ],
        ],
        'servers' => [
            'edit' => [
                'buttons' => [
                    'save'   => 'Änderungen speicher',
                    'cancel' => 'Abbrechen',
                    'delete' => 'Server löschen',
                ],
            ],
        ],
    ],

];
