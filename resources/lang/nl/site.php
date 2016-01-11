<?php

return [

    'admin' => [
        'users'   => [
            'listing' => [
                'table'  => [
                    'col1' => 'Gebruikersnaam',
                    'col2' => 'Email',
                    'col3' => 'Rol',
                    'col4' => 'Taal',
                    'col5' => 'Status',
                    'col6' => 'Aangemaakt',
                ],
                'status' => [
                    'active'   => 'Actief',
                    'inactive' => 'Inactief',
                ],
            ],
            'edit'    => [
                'details' => 'Details',
                'buttons' => [
                    'save'   => 'Opslaan',
                    'cancel' => 'Annuleren',
                    'delete' => 'Gebruiker Verwijderen',
                ],
                'inputs'  => [
                    'username'       => ['label' => 'Gebruikersnaam'],
                    'email'          => ['label' => 'Email'],
                    'role'           => ['label' => 'Rol'],
                    'account_status' => ['label' => 'Status'],
                    'lang'           => ['label' => 'Taal'],
                    'genpass'        => ['label' => 'Genereer nieuw wachtwoord'],
                ],
            ],
            'updates' => [
                'password' => [
                    'generated' => 'Nieuw wachtwoord gestuurd naar :username (:email).',
                ],
            ],
        ],
        'roles'   => [
            'edit'   => [
                'buttons' => [
                    'save'   => 'Opslaan',
                    'cancel' => 'Annuleren',
                    'delete' => 'Rol Verwijderen',
                ],
            ],
            'create' => [
                'buttons' => [
                    'save'   => 'Rol Aanmaken',
                    'cancel' => 'Annuleren',
                    'delete' => 'Rol Verwijderen',
                ],
            ],
        ],
        'servers' => [
            'edit' => [
                'buttons' => [
                    'save'   => 'Opslaan',
                    'cancel' => 'Cancel',
                    'delete' => 'Server Verwijderen',
                ],
            ],
        ],
    ],

];
