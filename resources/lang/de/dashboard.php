<?php

return [

    'metro'                         => [
        'players_online' => 'Spieler auf den Servern',
        'average_bans'   => 'Ca. Banns pro Tag',
        'yesterday_bans' => 'Banns gestern',
        'player_count'   => 'Spieler insgesamt',
        'adkats'         => [
            'titles'        => [
                'killed'        => 'Getötete Spieler',
                'kicked'        => 'Gekickte Spieler',
                'banned'        => 'Gebannte Spieler',
                'banned_active' => 'Aktive Banns',
            ],
            'killed'        => '% :killed  der Spieler wurden getötet',
            'kicked'        => '% :kicked  der Spieler wurden gekickt',
            'banned'        => '% :banned  der Spieler wurden gebannt',
            'banned_active' => '% :banned der Spieler sind gebannt',
        ],
    ],
    'population'                    => [
        'title'   => 'Bevölkerung',
        'footer'  => 'Gesamt',
        'columns' => [
            'col1' => 'Server',
            'col2' => 'Online',
            'col3' => 'Karte',
        ],
    ],
    'bans'                          => [
        'title'   => 'Neuste Banns',
        'columns' => [
            'col1' => 'Spieler',
            'col2' => 'Admin',
            'col3' => 'ausgeführt',
            'col4' => 'läuft ab',
        ],
    ],
    'players_seen_country_past_day' => [
        'title' => 'Spieler nach Land (24h)',
        'table' => [
            'col1' => 'das Land',
            'col2' => 'Todays Besuche',
            'col3' => 'Yesterdays Besuche',
        ],
    ],
];
