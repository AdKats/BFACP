<?php

return [

    'bans'            => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Spel',
                'col3' => 'Speler',
                'col4' => 'Admin',
                'col5' => 'Status',
                'col6' => 'Sinds',
                'col7' => 'Verloopt',
                'col8' => 'Geforceerd',
                'col9' => 'Reden',
            ],
        ],
        'edit'    => [
            'fields'  => [
                'field1'  => 'Speler',
                'field2'  => 'Admin',
                'field3'  => 'Notities',
                'field4'  => 'Reden',
                'field5'  => 'Server',
                'field6'  => 'Datum &amp; Tijd',
                'field7'  => 'Status',
                'field8'  => 'Ban Type',
                'field9'  => 'forceer op GUID',
                'field10' => 'forceer op Name',
                'field11' => 'forceer op IP',
            ],
            'buttons' => [
                'submit'  => [
                    'text1' => 'Opslaan',
                    'text2' => 'Even geduld alstublieft...',
                    'text3' => 'Ban Opheffen',
                ],
                'cancel'  => 'Annuleer',
                'profile' => 'Terug naar profiel',
            ],
        ],
        'prompts' => [
            'unban' => [
                'request_failed' => 'Verzoek mislukt. Probeer het later opnieuw.',
                'reason'         => 'Geef de reden voor het opheffen van de ban',
                'notes'          => 'Would you like to update the ban notes?\nClick cancel to keep current notes.',
            ],
        ],
    ],
    'special_players' => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Spel',
                'col3' => 'Speler',
                'col4' => 'Group',
                'col5' => 'Created',
                'col6' => 'Verloopt',
            ],
        ],
    ],
    'users'           => [
        'no_soldiers' => 'Geen Soldaten Gekoppeld.',
        'no_users'    => 'Geen gebruikers gevonden.',
        'soldiers'    => 'Soldaten',
        'listing'     => [
            'buttons' => [
                'create' => 'Gebruiker Toevoegen',
            ],
            'table'   => [
                'col1' => 'Gebruiker',
                'col2' => 'Email',
                'col3' => 'Rol',
                'col4' => 'Vervaltijd',
                'col5' => 'Soldaten',
                'col6' => 'Notities',
            ],
        ],
        'edit'        => [
            'details' => 'Details',
            'buttons' => [
                'save'   => 'Opslaan',
                'cancel' => 'Annuleer',
                'delete' => 'Gebruiker Verwijderen',
            ],
            'inputs'  => [
                'username'   => [
                    'label' => 'Gebruikersnaam',
                ],
                'email'      => [
                    'label' => 'Email',
                ],
                'role'       => [
                    'label' => 'Rol',
                ],
                'expiration' => [
                    'label' => 'Vervaltijd',
                    'help'  => 'Laat datum leeg voor standaard vervaltijd.',
                ],
                'notes'      => [
                    'label' => 'Notities',
                ],
                'soldiers'   => [
                    'label' => 'Speler IDs',
                    'help'  => 'Gebruik een komma tussen IDs om meerdere spelers te koppelen. Verwijder IDs om ze te ontkoppelen.',
                ],
                'soldier'    => [
                    'label' => 'Spelers Naam',
                    'help'  => 'Vul de spelersnaam in om het systeem het ID op te laten zoeken. Dit voegt iedere speler toe met de opgegeven naam.',
                ],
            ],
            'table'   => [
                'col1' => 'ID',
                'col2' => 'Spel',
                'col3' => 'Naam',
            ],
        ],
    ],

];
