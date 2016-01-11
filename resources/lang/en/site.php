<?php

return [

    'admin' => [
        'users'   => [
            'listing' => [
                'table'  => [
                    'col1' => 'Username',
                    'col2' => 'Email',
                    'col3' => 'Role',
                    'col4' => 'Language',
                    'col5' => 'Status',
                    'col6' => 'Created',
                ],
                'status' => [
                    'active'   => 'Active',
                    'inactive' => 'Inactive',
                ],
            ],
            'edit'    => [
                'details' => 'Details',
                'buttons' => [
                    'save'   => 'Save Changes',
                    'cancel' => 'Cancel',
                    'delete' => 'Delete User',
                ],
                'inputs'  => [
                    'username'       => ['label' => 'Username'],
                    'email'          => ['label' => 'Email'],
                    'role'           => ['label' => 'Role'],
                    'account_status' => ['label' => 'Status'],
                    'lang'           => ['label' => 'Language'],
                    'genpass'        => ['label' => 'Generate new password for user'],
                ],
            ],
            'updates' => [
                'password' => [
                    'generated' => ':username (:email) has been emailed with their new password.',
                ],
            ],
            'create'  => [
                'details' => 'Details',
                'inputs'  => [
                    'username' => ['label' => 'Username'],
                    'email'    => ['label' => 'Email'],
                    'role'     => ['label' => 'Role'],
                    'lang'     => ['label' => 'Language'],
                ],
                'buttons' => [
                    'save'   => 'Create User',
                    'cancel' => 'Cancel',
                ],
            ],
        ],
        'roles'   => [
            'edit'   => [
                'buttons' => [
                    'save'   => 'Save Changes',
                    'cancel' => 'Cancel',
                    'delete' => 'Delete Role',
                ],
            ],
            'create' => [
                'buttons' => [
                    'save'   => 'Create Role',
                    'cancel' => 'Cancel',
                    'delete' => 'Delete Role',
                ],
            ],
        ],
        'servers' => [
            'edit' => [
                'buttons' => [
                    'save'   => 'Save Changes',
                    'cancel' => 'Cancel',
                    'delete' => 'Delete Server',
                ],
            ],
        ],
    ],

];
