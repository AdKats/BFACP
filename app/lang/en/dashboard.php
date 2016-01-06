<?php

return [

    'metro'                         => [
        'players_online' => 'Players Online',
        'average_bans'   => 'Average Bans Per Day',
        'yesterday_bans' => 'Bans Yesterday',
        'player_count'   => 'Unique Players',
        'adkats'         => [
            'titles'        => [
                'killed'        => 'Players Killed',
                'kicked'        => 'Players Kicked',
                'banned'        => 'Players Banned',
                'banned_active' => 'Active Bans',
            ],
            'killed'        => ':killed% of players have been killed',
            'kicked'        => ':kicked% of players have been kicked',
            'banned'        => ':banned% of players have been banned',
            'banned_active' => ':banned% of players are banned',
        ],
    ],
    'motd'                          => 'Message of the Day',
    'online_admin'                  => 'Online Admins',
    'population'                    => [
        'title'   => 'Population',
        'footer'  => 'Total',
        'columns' => [
            'col1' => 'Server',
            'col2' => 'Online',
            'col3' => 'Map',
        ],
    ],
    'bans'                          => [
        'title'   => 'Latest Bans',
        'columns' => [
            'col1' => 'Player',
            'col2' => 'Admin',
            'col3' => 'Issued',
            'col4' => 'Expires',
        ],
    ],
    'players_seen_country_past_day' => [
        'title' => 'Players Seen by Country (24h)',
        'table' => [
            'col1' => 'Country',
            'col2' => 'Visits',
        ],
    ],
];
