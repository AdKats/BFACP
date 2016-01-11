<?php

return [

    'bans'            => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Spiel',
                'col3' => 'Spieler',
                'col4' => 'Admin',
                'col5' => 'Status',
                'col6' => 'Ausgeführt',
                'col7' => 'Läuft ab',
                'col8' => 'Gebannt per',
                'col9' => 'Grund',
            ],
        ],
        'edit'    => [
            'fields'  => [
                'field1'  => 'Spieler',
                'field2'  => 'Admin',
                'field3'  => 'Notizen',
                'field4'  => 'Grund',
                'field5'  => 'Server',
                'field6'  => 'Datum &amp; Zeit',
                'field7'  => 'Status',
                'field8'  => 'Bann Typ',
                'field9'  => 'Ausführen durch GUID',
                'field10' => 'Ausführen durch Name',
                'field11' => 'Ausführen durch IP',
            ],
            'buttons' => [
                'submit'  => [
                    'text1' => 'Speichere Änderungen',
                    'text2' => 'Bitte warten...',
                    'text3' => 'Entbannen',
                ],
                'cancel'  => 'Abbrechen',
                'profile' => 'Zurück zum Spielerprofil',
            ],
            'unban'   => [
                'request_failed' => 'Anfrage fehlgeschlagen. Bitte versuchen Sie es später noch einmal.',
                'prompt'         => 'Entbannungsgrund eingeben',
            ],
        ],
    ],
    'special_players' => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Spiel',
                'col3' => 'Spieler',
                'col4' => 'Gruppe',
                'col5' => 'Erstellt',
                'col6' => 'Läuft ab',
            ],
        ],
    ],
    'users'           => [
        'no_soldiers' => 'Keine Soldaten zugewiesen.',
        'no_users'    => 'Keine Benutzer gefunden.',
        'soldiers'    => 'Soldaten',
        'listing'     => [
            'buttons' => [
                'create' => 'Benutzer hinzufügen',
            ],
            'table'   => [
                'col1' => 'Benutzer',
                'col2' => 'Email',
                'col3' => 'Rolle',
                'col4' => 'Ablauf',
                'col5' => 'Soldaten',
                'col6' => 'Notizen',
            ],
        ],
        'edit'        => [
            'details' => 'Details',
            'buttons' => [
                'save'   => 'Änderungen speichern',
                'cancel' => 'Abbrechen',
                'delete' => 'Benutzer löschen',
            ],
            'inputs'  => [
                'username'   => [
                    'label' => 'Benutzername',
                ],
                'email'      => [
                    'label' => 'Email',
                ],
                'role'       => [
                    'label' => 'Rolle',
                ],
                'expiration' => [
                    'label' => 'Ablauf',
                    'help'  => 'Datum leer lassen um Standardablaufdatum zu setzen.',
                ],
                'notes'      => [
                    'label' => 'Notizen',
                ],
                'soldiers'   => [
                    'label' => 'Spieler IDs',
                    'help'  => 'IDs durch ein Komma trennen, um mehr Spieler hinzuzufügen. IDs entfernen, um sie vom Benutzer zu löschen.',
                ],
                'soldier'    => [
                    'label' => 'Spielername',
                    'help'  => 'Damit das System Spieler hinzufügt, geben Sie den Spielernamen ein. Dadurch werden alle Spieler mit dem Namen hinzugefügt.',
                ],
            ],
            'table'   => [
                'col1' => 'ID',
                'col2' => 'Spiel',
                'col3' => 'Name',
            ],
        ],
    ],

];
