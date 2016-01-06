<?php

return [

    'profile' => [

        /**
         * Details Block
         */
        'details'     => [
            'title'  => 'Details',
            'items'  => [
                'id'              => 'ID',
                'game'            => 'Game',
                'eaguid'          => 'EA GUID',
                'pbguid'          => 'PB GUID',
                'ip'              => 'IP',
                'country'         => 'Country',
                'reputation'      => 'Reputation',
                'rank'            => 'Rank',
                'linked_accounts' => 'Linked Accounts',
            ],
            'cached' => 'Viewing Cached Version',
        ],
        'links'       => [
            'title' => 'Links',
            'items' => [

                /**
                 * External Links
                 */
                'battlelog'    => 'Battlelog',
                'istats'       => 'I-Stats',
                'anticheatinc' => 'Anticheat Inc.',
                'bf4db'        => 'BF4DB',
                'bf3stats'     => 'BF3 Stats',
                'bf4stats'     => 'BF4 Stats',
                'bfhstats'     => 'BFH Stats',
                'metabans'     => 'Metabans',
                'pbbans'       => 'PBBans',
                /**
                 * Internal LInks
                 */
                'chatlogs'     => 'Chatlogs',
            ],
        ],
        /**
         * Infractions and Bans Blocks
         */
        'infractions' => [
            'title'   => 'Infractions',
            'none'    => 'No infractions on file',
            'overall' => [
                'title' => 'Total',
            ],
            'table'   => [
                'col1' => 'Server',
                'col2' => 'Punishes',
                'col3' => 'Forgives',
                'col4' => 'Total',
                'col5' => 'Next Punishment',
            ],
            'table2'  => [
                'col1' => 'Type',
                'col2' => 'Action',
                'col3' => 'By',
                'col4' => 'Issued',
                'col5' => 'Reason',
            ],
        ],
        'bans'        => [
            'type'     => [
                'temporary' => [
                    'long'  => 'Temporary',
                    'short' => 'Temp',
                ],
                'permanent' => [
                    'long'  => 'Permanent',
                    'short' => 'Perm',
                ],
            ],
            'status'   => [
                'enabled'  => 'Enabled',
                'disabled' => 'Disabled',
                'expired'  => 'Expired',
                'expire'   => 'Expire',
            ],
            'current'  => [
                'title'    => 'Current Ban',
                'none'     => 'No bans on file',
                'inactive' => 'No ban currently in effect. Status&colon; <strong>:status</strong>',
                'table'    => [
                    'col1' => 'Issued',
                    'col2' => 'Expires',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Status',
                    'col6' => 'Reason',
                ],
            ],
            'previous' => [
                'title' => 'Previous Bans',
                'none'  => 'No previous bans on file',
                'table' => [
                    'col1' => 'Issued',
                    'col2' => 'Duration',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Reason',
                ],
            ],
        ],
        /**
         * Stats
         */
        'stats'       => [

            /**
             * Player Server Stats
             */
            'server'   => [
                'title' => 'Server Stats',
                'table' => [
                    'col1'  => 'First Seen',
                    'col2'  => 'Last Seen',
                    'col3'  => 'Overall Score',
                    'col4'  => 'Highest Score',
                    'col5'  => 'Kills',
                    'col6'  => 'HS',
                    'col7'  => 'Deaths',
                    'col8'  => 'Suicides',
                    'col9'  => 'Tks',
                    'col10' => 'Playtime',
                    'col11' => 'Rounds',
                    'col12' => 'Killstreak',
                    'col13' => 'Deathstreak',
                    'col14' => 'Wins',
                    'col15' => 'Losses',
                    'col16' => 'Server',
                    'extra' => [
                        'kd'   => 'K/D',
                        'hskr' => 'HSKR',
                        'wlr'  => 'W/L',
                    ],
                ],
            ],
            /**
             * Player sessions
             */
            'sessions' => [
                'title' => 'Sessions',
            ],
        ],
        /**
         * Anti-Cheat System
         */
        'acs'         => [
            'title'    => 'Suspicious Weapons',
            'help'     => 'Weapons shown are not always suspicious and is only there to help you decided if the player is cheating. Shotguns and Snipers may trigger more frequently.',
            'none'     => 'No Suspicious Weapons Found',
            'checking' => 'Checking player&hellip;',
            'table'    => [
                'col1'  => 'Weapon',
                'col2'  => 'Category',
                'col3'  => 'Kills',
                'col4'  => 'Headshots',
                'col5'  => 'Fired',
                'col6'  => 'Hit',
                'col7'  => 'Accuracy',
                'col8'  => 'Playtime',
                'col9'  => 'DPS',
                'col10' => 'HSKP',
                'col11' => 'KPM',
            ],
        ],
        /**
         * Player Records
         */
        'records'     => [
            'title'   => 'Record History',
            'viewing' => [
                'p1' => 'Viewing records',
                'p2' => 'through',
                'p3' => 'out of',
            ],
            'table'   => [
                'col1' => 'Date',
                'col2' => 'CMD Issued',
                'col3' => 'CMD Taken',
                'col4' => 'Target',
                'col5' => 'Source',
                'col6' => 'Server',
                'col7' => 'Message',
            ],
        ],
        /**
         * Charts
         */
        'charts'      => [
            'command_overview' => [
                'title' => 'CMD Usage',
                'chart' => [
                    'title'   => 'Command Usage Overview',
                    'tooltip' => 'Usage',
                ],
            ],
            'aliases'          => [
                'title' => 'Aliases',
                'chart' => [
                    'tooltip' => 'Alias',
                ],
            ],
            'ip_history'       => [
                'title' => 'IP History',
                'chart' => [
                    'tooltip' => 'IP',
                ],
            ],
        ],
    ],
];
