<?php

return [

    'bans'            => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Game',
                'col3' => 'Player',
                'col4' => 'Admin',
                'col5' => 'Status',
                'col6' => 'Issued',
                'col7' => 'Expires',
                'col8' => 'Enforcement',
                'col9' => 'Reason',
            ],
        ],
        'edit'    => [
            'fields'  => [
                'field1'  => 'Player',
                'field2'  => 'Admin',
                'field3'  => 'Notes',
                'field4'  => 'Reason',
                'field5'  => 'Server',
                'field6'  => 'Date &amp; Time',
                'field7'  => 'Status',
                'field8'  => 'Ban Type',
                'field9'  => 'Enforce by GUID',
                'field10' => 'Enforce by Name',
                'field11' => 'Enforce by IP',
            ],
            'buttons' => [
                'submit'  => [
                    'text1' => 'Save Changes',
                    'text2' => 'Please wait...',
                    'text3' => 'Unban',
                ],
                'cancel'  => 'Cancel',
                'profile' => 'Return to Player Profile',
            ],
        ],
        'prompts' => [
            'unban' => [
                'request_failed' => 'Request failed. Please try again later.',
                'reason'         => 'Enter unban reason',
                'notes'          => 'Would you like to update the ban notes?\nClick cancel to keep current notes.',
            ],
        ],
    ],
    'special_players' => [
        'listing' => [
            'table' => [
                'col1' => 'ID',
                'col2' => 'Game',
                'col3' => 'Player',
                'col4' => 'Group',
                'col5' => 'Created',
                'col6' => 'Expires',
            ],
        ],
    ],
    'users'           => [
        'no_soldiers' => 'No Soldiers Assigned.',
        'no_users'    => 'No users found.',
        'soldiers'    => 'Soldiers',
        'listing'     => [
            'buttons' => [
                'create' => 'Add User',
            ],
            'table'   => [
                'col1' => 'User',
                'col2' => 'Email',
                'col3' => 'Role',
                'col4' => 'Expiration',
                'col5' => 'Soldiers',
                'col6' => 'Notes',
            ],
        ],
        'edit'        => [
            'details' => 'Details',
            'buttons' => [
                'save'   => 'Save Changes',
                'cancel' => 'Cancel',
                'delete' => 'Delete User',
            ],
            'inputs'  => [
                'username'   => [
                    'label' => 'Username',
                ],
                'email'      => [
                    'label' => 'Email',
                ],
                'role'       => [
                    'label' => 'Role',
                ],
                'expiration' => [
                    'label' => 'Expiration',
                    'help'  => 'Leave date blank to set default expire date.',
                ],
                'notes'      => [
                    'label' => 'Notes',
                ],
                'soldiers'   => [
                    'label' => 'Player IDs',
                    'help'  => 'Seprate IDs by a comma to add more players. Remove IDs to delete them from the user.',
                ],
                'soldier'    => [
                    'label' => 'Player Name',
                    'help'  => 'To have the system add the player, type in the player name. This will add any player with the name provided.',
                ],
            ],
            'table'   => [
                'col1' => 'ID',
                'col2' => 'Game',
                'col3' => 'Name',
            ],
        ],
    ],

];
