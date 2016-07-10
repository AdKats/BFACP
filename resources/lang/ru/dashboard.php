<?php

return [

    'metro'                         => [
        'players_online' => 'Игроков в Сети',
        'average_bans'   => 'Среднее Кол-во Баннов в День',
        'yesterday_bans' => 'Банов Вчера',
        'player_count'   => 'Уникальных Игроков',
        'adkats'         => [
            'titles'        => [
                'killed'        => 'Игроков Убито',
                'kicked'        => 'Игроков Исключено',
                'banned'        => 'Игроков Забанено',
                'banned_active' => 'Активных Банов',
            ],
            'killed'        => ':killed% игроков было убито',
            'kicked'        => ':kicked% игроков было исключено',
            'banned'        => ':banned% игроков было забанено',
            'banned_active' => ':banned% игроков забанено',
        ],
    ],
    'motd'                          => 'Сообщение Дня',
    'online_admin'                  => 'Админы в Сети',
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
        'title'   => 'Последние Баны',
        'columns' => [
            'col1' => 'Игрок',
            'col2' => 'Админ',
            'col3' => 'Забанен',
            'col4' => 'Истекает',
        ],
    ],
    'players_seen_country_past_day' => [
        'title' => 'Игроки по Странам (24h)',
        'table' => [
            'col1' => 'Страна',
            'col2' => 'Todays Игроков',
            'col3' => 'Вчерашние Игроков',
        ],
    ],
];
