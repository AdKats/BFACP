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
                'game'            => 'Spel',
                'eaguid'          => 'EA GUID',
                'pbguid'          => 'PB GUID',
                'ip'              => 'IP',
                'country'         => 'Land',
                'reputation'      => 'Reputatie',
                'rank'            => 'Rank',
                'linked_accounts' => 'Verbonden Spelers',
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
            'title'   => 'Overtredingen',
            'none'    => 'Geen overtredingen bekend',
            'overall' => [
                'title' => 'Totaal',
            ],
            'table'   => [
                'col1' => 'Server',
                'col2' => 'Straffen',
                'col3' => 'Vergevingen',
                'col4' => 'Totaal',
                'col5' => 'Next Punishment',
            ],
            'table2'  => [
                'col1' => 'Type',
                'col2' => 'Actie',
                'col3' => 'Door',
                'col4' => 'Uitgegeven',
                'col5' => 'Reden',
            ],
        ],
        'bans'        => [
            'type'     => [
                'temporary' => [
                    'long'  => 'Tijdelijk',
                    'short' => 'Temp',
                ],
                'permanent' => [
                    'long'  => 'Permanent',
                    'short' => 'Perm',
                ],
            ],
            'status'   => [
                'enabled'  => 'Ingeschakeld',
                'disabled' => 'Uitgeschakeld',
                'expired'  => 'Verlopen',
                'expire'   => 'Verloopt',
            ],
            'current'  => [
                'title'    => 'Actieve Ban',
                'none'     => 'Geen ban(s) bekend',
                'inactive' => 'Geen ban op dit moment actief. Status&colon; <strong>:status</strong>',
                'table'    => [
                    'col1' => 'Sinds',
                    'col2' => 'Verloopt',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Status',
                    'col6' => 'Reden',
                ],
            ],
            'previous' => [
                'title' => 'Eerdere Bans',
                'none'  => 'Geen eerdere ban(s) bekend',
                'table' => [
                    'col1' => 'Sinds',
                    'col2' => 'Duur',
                    'col3' => 'Server',
                    'col4' => 'Type',
                    'col5' => 'Reden',
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
                    'col1'  => 'Eerste Bezoek',
                    'col2'  => 'Laatste Bezoek',
                    'col3'  => 'Totaal Score',
                    'col4'  => 'Hoogste Score',
                    'col5'  => 'Kills',
                    'col6'  => 'HS',
                    'col7'  => 'Deaths',
                    'col8'  => 'Suicides',
                    'col9'  => 'Tks',
                    'col10' => 'Speeltijd',
                    'col11' => 'Rondes',
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
                'title' => 'Sessies',
            ],
        ],
        /**
         * Anti-Cheat System
         */
        'acs'         => [
            'title'    => 'Verdachte Wapens',
            'help'     => 'Getoonde wapens zijn niet altijd verdacht en staan hier alleen om u te helpen besluiten of de speler cheat. Shotguns en Snipers zullen vaker getoond worden.',
            'none'     => 'Geen verdachte wapens gevonden.',
            'checking' => 'Speler controleren&hellip;',
            'table'    => [
                'col1'  => 'Wapen',
                'col2'  => 'Categorie',
                'col3'  => 'Kills',
                'col4'  => 'Headshots',
                'col5'  => 'Geschoten',
                'col6'  => 'Geraakt',
                'col7'  => 'Nauwkeurigheid',
                'col8'  => 'Speeltijd',
                'col9'  => 'DPS',
                'col10' => 'HSKP',
                'col11' => 'KPM',
            ],
        ],
        /**
         * Player Records
         */
        'records'     => [
            'title'   => 'Record Geschiedenis',
            'viewing' => [
                'p1' => 'Toont records',
                'p2' => 'tot',
                'p3' => 'van',
            ],
            'table'   => [
                'col1' => 'Datum',
                'col2' => 'Opgegeven CMD',
                'col3' => 'uitgevoerd CMD',
                'col4' => 'Doel',
                'col5' => 'Bron',
                'col6' => 'Server',
                'col7' => 'Bericht',
            ],
        ],
        /**
         * Charts
         */
        'charts'      => [
            'command_overview' => [
                'title' => 'CMD Gebruik',
                'chart' => [
                    'title'   => 'Overzicht Gebruikte Commandos',
                    'tooltip' => 'Gebruik',
                ],
            ],
            'aliases'          => [
                'title' => 'Aliassen',
                'chart' => [
                    'tooltip' => 'Alias',
                ],
            ],
            'ip_history'       => [
                'title' => 'IP Geschiedenis',
                'chart' => [
                    'tooltip' => 'IP',
                ],
            ],
        ],
    ],
];
