<?php

return [

    'metro'                         => [
        'players_online' => 'Игроков в сети',
        'average_bans'   => 'Среднее кол-во баннов в день',
        'yesterday_bans' => 'Банов вчера',
        'player_count'   => 'Уникальных игроков',
        'adkats'         => [
            'titles'        => [
                'killed'        => 'Игроков убито',
                'kicked'        => 'Игроков исключено',
                'banned'        => 'Игроков забанено',
                'banned_active' => 'Активных банов',
            ],
            'killed'        => ':killed% игроков было убито',
            'kicked'        => ':kicked% игроков было исключено',
            'banned'        => ':banned% игроков было забанено',
            'banned_active' => ':banned% игроков забанено',
        ],
    ],
    'motd'                          => 'Сообщение дня',
    'online_admin'                  => 'Админов в сети',
    'population'                    => [
        'title'   => 'Онлайн',
        'footer'  => 'Всего',
        'columns' => [
            'col1' => 'Сервер',
            'col2' => 'Онлайн',
            'col3' => 'Карта',
        ],
    ],
    'bans'                          => [
        'title'   => 'Последние баны',
        'columns' => [
            'col1' => 'Игрок',
            'col2' => 'Админ',
            'col3' => 'Забанен',
            'col4' => 'Истекает',
        ],
    ],
    'players_seen_country_past_day' => [
        'title' => 'Игроки заходившие на сервер из разных стран (24h)',
        'table' => [
            'col1' => 'Страна',
            'col2' => 'Игроков',
        ],
    ],
];
