<?php

return [

    'profile' => [

        /**
         * Details Block
         */
        'details' => [
            'title' => 'Details',
            'items' => [
                'id'         => 'ID',
                'game'       => 'Game',
                'eaguid'     => 'EA GUID',
                'pbguid'     => 'PB GUID',
                'ip'         => 'IP',
                'country'    => 'Country',
                'reputation' => 'Reputation',
                'rank'       => 'Rank'
            ]
        ],

        /**
         * Infractions and Bans Blocks
         */
        'infractions' => [
            'title' => 'Infractions',
            'none' => 'No infractions on file',
            'overall' => [
                'title' => 'Total'
            ],
            'table' => [
                'col1' => 'Server',
                'col2' => 'Punishes',
                'col3' => 'Forgives',
                'col4' => 'Total'
            ]
        ],

        'bans' => [
            'type' => [
                'temporary' => [
                    'long' => 'Temporary',
                    'short' => 'Temp'
                ],
                'permanent' => [
                    'long' => 'Permanent',
                    'short' => 'Perm'
                ]
            ],
            'status' => [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
                'expired' => 'Expired',
                'expire' => 'Expire'
            ],
            'current' => [
                'title' => 'Current Ban',
                'none' => 'No bans on file',
                'table' => [
                    'col1' => 'Issued',
                    'col2' => 'Expires',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Status',
                    'col6' => 'Reason'
                ]
            ],

            'previous' => [
                'title' => 'Previous Bans',
                'none' => 'No previous bans on file',
                'table' => [
                    'col1' => 'Issued',
                    'col2' => 'Expires',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Reason'
                ]
            ]
        ],

        /**
         * Stats
         */
        'stats' => [

            /**
             * Player Server Stats
             */
            'server' => [
                'table' => [
                    'col1' => 'First Seen',
                    'col2' => 'Last Seen',
                    'col3' => 'Overall Score',
                    'col4' => 'Highest Score',
                    'col5' => 'Kills',
                    'col6' => 'HS',
                    'col7' => 'Deaths',
                    'col8' => 'Suicides',
                    'col9' => 'Tks',
                    'col10' => 'Playtime',
                    'col11' => 'Rounds',
                    'col12' => 'Killstreak',
                    'col13' => 'Deathstreak',
                    'col14' => 'Wins',
                    'col15' => 'Losses',
                    'col16' => 'Server',
                    'extra' => [
                        'kd' => 'K/D',
                        'hskr' => 'HSKR',
                        'wlr' => 'W/L'
                    ]
                ]
            ]
        ]
    ]
];
