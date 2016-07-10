<?php

return [

    'profile' => [

        /**
         * Details Block
         */
        'details'     => [
            'title'  => 'Подробно',
            'items'  => [
                'id'              => 'ID',
                'game'            => 'Игра',
                'eaguid'          => 'EA GUID',
                'pbguid'          => 'PB GUID',
                'ip'              => 'IP',
                'country'         => 'Страна',
                'reputation'      => 'Репутация',
                'rank'            => 'Уровень',
                'linked_accounts' => 'Ассоциированые Аккаунты',
            ],
            'cached' => 'Вы Смотрите Старую Версию',
        ],
        'links'       => [
            'title' => 'Ресурсы',
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
                'fairplay'     => '24/7 Fairplay',
                /**
                 * Internal LInks
                 */
                'chatlogs'     => 'Чат',
            ],
        ],
        /**
         * Infractions and Bans Blocks
         */
        'infractions' => [
            'title'   => 'Нарушения',
            'none'    => 'Нет нарушений',
            'overall' => [
                'title' => 'Всего',
            ],
            'table'   => [
                'col1' => 'Сервер',
                'col2' => 'Наказаний',
                'col3' => 'Прощений',
                'col4' => 'Всего',
                'col5' => 'Следующее наказание',
            ],
            'table2'  => [
                'col1' => 'Тип',
                'col2' => 'Действия',
                'col3' => 'Кем',
                'col4' => 'Применено',
                'col5' => 'Причина',
            ],
        ],
        'bans'        => [
            'type'     => [
                'temporary' => [
                    'long'  => 'Временный',
                    'short' => 'Врем',
                ],
                'permanent' => [
                    'long'  => 'Вечный',
                    'short' => 'Вечн',
                ],
            ],
            'status'   => [
                'enabled'  => 'Включен',
                'disabled' => 'Выключен',
                'expired'  => 'Истек',
                'expire'   => 'Истекает',
            ],
            'current'  => [
                'title'    => 'Текущий Бан',
                'none'     => 'Нет бана',
                'inactive' => 'В данный момент нет активных банов. Статус&colon; <strong>:status</strong>',
                'table'    => [
                    'col1' => 'Забанен',
                    'col2' => 'Истекает',
                    'col3' => 'Сервер',
                    'col4' => 'Тип',
                    'col5' => 'Статус',
                    'col6' => 'Причина',
                ],
            ],
            'previous' => [
                'title' => 'Истекшие Баны',
                'none'  => 'Нет истекших банов',
                'table' => [
                    'col1' => 'Забанен',
                    'col2' => 'Длительность',
                    'col3' => 'Сервер',
                    'col4' => 'Тип',
                    'col5' => 'Причина',
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
                'title' => 'Статистика Сервера',
                'table' => [
                    'col1'  => 'Впервые Зашел',
                    'col2'  => 'Выход',
                    'col3'  => 'Сред Счет',
                    'col4'  => 'Макс Счет',
                    'col5'  => 'Убийств',
                    'col6'  => 'Хедшотов',
                    'col7'  => 'Смертей',
                    'col8'  => 'Суицидов',
                    'col9'  => 'Тимкилов',
                    'col10' => 'Время',
                    'col11' => 'Раундов',
                    'col12' => 'Килстрик',
                    'col13' => 'Детстрик',
                    'col14' => 'Побед',
                    'col15' => 'Поражений',
                    'col16' => 'Сервер',
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
                'title' => 'Сессии',
            ],
        ],
        /**
         * Anti-Cheat System
         */
        'acs'         => [
            'title'    => 'Подозрительная Статистика',
            'help'     => 'Статистика в этом списке не дает верно определить читера. Список помогает только определить его. Дробовики и болтовки отображаются не совсем корректно.',
            'none'     => 'Подозрительной Статистики Нет',
            'checking' => 'Проверка игрока&hellip;',
            'table'    => [
                'col1'  => 'Оружие',
                'col2'  => 'Категория',
                'col3'  => 'Убийств',
                'col4'  => 'Хедшотов',
                'col5'  => 'Выстрелов',
                'col6'  => 'Попаданий',
                'col7'  => 'Точность',
                'col8'  => 'Время',
                'col9'  => 'DPS',
                'col10' => 'HSKP',
                'col11' => 'KPM',
            ],
        ],
        /**
         * Player Records
         */
        'records'     => [
            'title'   => 'История Команд',
            'viewing' => [
                'p1' => 'Просмотр команд с',
                'p2' => 'по',
                'p3' => 'из',
            ],
            'table'   => [
                'col1' => 'Дата',
                'col2' => 'Запрос',
                'col3' => 'Действие',
                'col4' => 'Цель',
                'col5' => 'От',
                'col6' => 'Сервер',
                'col7' => 'Сообщение',
            ],
        ],
        /**
         * Charts
         */
        'charts'      => [
            'command_overview' => [
                'title' => 'Использование Команд',
                'chart' => [
                    'title'   => 'График Использования Команд',
                    'tooltip' => 'Доля',
                ],
            ],
            'aliases'          => [
                'title' => 'Синонимы',
                'chart' => [
                    'tooltip' => 'Синоним',
                ],
            ],
            'ip_history'       => [
                'title' => 'История IP',
                'chart' => [
                    'tooltip' => 'IP',
                ],
            ],
        ],
    ],
    'admin' => [
        'forgive' => [
            'warnings' => [
                'overage' => 'Вы простили :player :usertotal раз, но прощения были уменьшены до :reduced чтобы соответствовать кол-ву нарушений. Система добавит остальные :remaining прощений на другой сервер, если это возможно.',
            ],
            'errors'   => [
                'err1' => 'Вы не можете простить :player, т.к. у него нет нарушений.',
            ],
        ],
    ],
];
