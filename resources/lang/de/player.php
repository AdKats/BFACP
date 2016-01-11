<?php

return [

    'profile' => [

        /**
         * Details Block
         */
        'details'     => [
            'title' => 'Details',
            'items' => [
                'id'         => 'ID',
                'game'       => 'Spiel',
                'eaguid'     => 'EA GUID',
                'pbguid'     => 'PB GUID',
                'ip'         => 'IP',
                'country'    => 'Land',
                'reputation' => 'Reputation',
                'rank'       => 'Rang',
            ],
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
            'title'   => 'Verstöße',
            'none'    => 'Keine Verstöße vorhanden.',
            'overall' => [
                'title' => 'Total',
            ],
            'table'   => [
                'col1' => 'Server',
                'col2' => 'Bestrafungen',
                'col3' => 'Vergebungen',
                'col4' => 'Total',
                'col5' => 'Next Punishment',
            ],
        ],
        'bans'        => [
            'type'     => [
                'temporary' => [
                    'long'  => 'Temporär',
                    'short' => 'Temp',
                ],
                'permanent' => [
                    'long'  => 'Permanent',
                    'short' => 'Perm',
                ],
            ],
            'status'   => [
                'enabled'  => 'Aktiviert',
                'disabled' => 'Deaktiviert',
                'expired'  => 'Abgelaufen',
                'expire'   => 'läuft ab',
            ],
            'current'  => [
                'title'    => 'Aktueller Bann',
                'none'     => 'Keine Banns vorhanden',
                'inactive' => 'Kein Bann aktuell aktiviert. Status&colon; <strong>:status</strong>',
                'table'    => [
                    'col1' => 'Ausgeführt',
                    'col2' => 'läuft ab',
                    'col3' => 'Server',
                    'col4' => 'Typ',
                    'col5' => 'Status',
                    'col6' => 'Grund',
                ],
            ],
            'previous' => [
                'title' => 'Vorherige Banns',
                'none'  => 'Keine vorherigen Banns vorhanden',
                'table' => [
                    'col1' => 'Ausgeführt',
                    'col2' => 'Dauer',
                    'col3' => 'Server',
                    'col4' => 'Typ',
                    'col5' => 'Grund',
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
                    'col1'  => 'Erfasst seit',
                    'col2'  => 'Zuletzt Gesehen',
                    'col3'  => 'Gesamtpunktzahl',
                    'col4'  => 'Höchste Punktzahl',
                    'col5'  => 'Kills',
                    'col6'  => 'HS',
                    'col7'  => 'Tode',
                    'col8'  => 'Selbstmorde',
                    'col9'  => 'Tks',
                    'col10' => 'Spielzeit',
                    'col11' => 'Runden',
                    'col12' => 'Killstreak',
                    'col13' => 'Deathstreak',
                    'col14' => 'Gewonnen Spiele',
                    'col15' => 'Verlorene Spiele',
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
                'title' => 'Sitzungen',
            ],
        ],
        /**
         * Anti-Cheat System
         */
        'acs'         => [
            'title'    => 'Auffällige Waffen',
            'help'     => 'Bei den rot markierten Waffen muss es sich nicht zwangsläufig um Cheat-Verdächtige Waffen handeln. Daher müssen diese Stats hier besonders bei Schrotflinten und Scharfschützengewehren mit Vorsicht verwendet werden.',
            'none'     => 'Keine auffälligen Waffen gefunden',
            'checking' => 'Überprüfe Spieler&hellip;',
            'table'    => [
                'col1'  => 'Waffe',
                'col2'  => 'Kategorie',
                'col3'  => 'Kills',
                'col4'  => 'Kopfschüsse',
                'col5'  => 'Abgefeuert',
                'col6'  => 'Treffer',
                'col7'  => 'Genauigkeit',
                'col8'  => 'Spielzeit',
                'col9'  => 'DPS',
                'col10' => 'HSKP',
                'col11' => 'KPM',
            ],
        ],
        /**
         * Player Records
         */
        'records'     => [
            'title'   => 'Historie',
            'viewing' => [
                'p1' => 'Anzeigen der Datensätze',
                'p2' => 'durch',
                'p3' => 'aus',
            ],
            'table'   => [
                'col1' => 'Datum',
                'col2' => 'CMD ausgeführt',
                'col3' => 'CMD erhalten',
                'col4' => 'Ziel',
                'col5' => 'Quelle',
                'col6' => 'Server',
                'col7' => 'Nachricht',
            ],
        ],
        /**
         * Charts
         */
        'charts'      => [
            'command_overview' => [
                'title' => 'CMD Benutzung',
                'chart' => [
                    'title'   => 'Übersicht der verwendeten Befehle',
                    'tooltip' => 'Benutzung',
                ],
            ],
            'aliases'          => [
                'title' => 'Aliase',
                'chart' => [
                    'tooltip' => 'Alias',
                ],
            ],
            'ip_history'       => [
                'title' => 'IP Historie',
                'chart' => [
                    'tooltip' => 'IP',
                ],
            ],
        ],
    ],
];
