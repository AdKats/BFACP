<?php

return [

    'profile' => [

        /**
         * Details Block
         */
        'details'     => [
            'title'  => 'Детали',
            'items'  => [
                'id'              => 'ID',
                'game'            => 'Игра',
                'eaguid'          => 'EA GUID',
                'pbguid'          => 'PB GUID',
                'ip'              => 'IP',
                'country'         => 'Страна',
                'reputation'      => 'Репутация',
                'rank'            => 'Уровень',
                'linked_accounts' => 'Ассоциированые аккаунты',
            ],
            'cached' => 'Просмотр кэшируемой версии',
        ],
        'links'       => [
            'title' => 'Ссылки',
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
                'chatlogs'     => 'Лог чата',
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
                'title'    => 'Текущий бан',
                'none'     => 'Нет бана',
                'inactive' => 'В данный момент нет активных банов. Статус&colon; <strong>:status</strong>',
                'table'    => [
                    'col1' => 'Применено',
                    'col2' => 'Истекает',
                    'col3' => 'Сервер',
                    'col4' => 'Тип',
                    'col5' => 'Статус',
                    'col6' => 'Причина',
                ],
            ],
            'previous' => [
                'title' => 'Прошлые баны',
                'none'  => 'Нет прошлых банов',
                'table' => [
                    'col1' => 'Применено',
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
                'title' => 'Статистика сервера',
                'table' => [
                    'col1'  => 'Впервые зашел',
                    'col2'  => 'Вышел',
                    'col3'  => 'Средний счет',
                    'col4'  => 'Максимальный счет',
                    'col5'  => 'Убийств',
                    'col6'  => 'В голову',
                    'col7'  => 'Смертей',
                    'col8'  => 'Самоубийств',
                    'col9'  => 'Убийств команды',
                    'col10' => 'Время игры',
                    'col11' => 'Раундов',
                    'col12' => 'Убийств подряд',
                    'col13' => 'Смертей подряд',
                    'col14' => 'Выйгрышей',
                    'col15' => 'Пройгрышей',
                    'col16' => 'Сервер',
                    'extra' => [
                        'kd'   => 'У/С',
                        'hskr' => 'HSKR',
                        'wlr'  => 'В/П',
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
            'title'    => 'Подозрительное оружие',
            'help'     => 'Оружие в этом списке не всегда подозрительно. Список помогает только определить читера. Дробовики и болтовки отображаются не совсем корректно.',
            'none'     => 'Подозрительного оружия не найдено',
            'checking' => 'Проверка игрока&hellip;',
            'table'    => [
                'col1'  => 'Оружие',
                'col2'  => 'Категория',
                'col3'  => 'Убийств',
                'col4'  => 'В голову',
                'col5'  => 'Выстрелов',
                'col6'  => 'Попаданий',
                'col7'  => 'Точность',
                'col8'  => 'Время игры',
                'col9'  => 'Урон за попадание',
                'col10' => 'HSKR',
                'col11' => 'Убийств в минуту',
            ],
        ],
        /**
         * Player Records
         */
        'records'     => [
            'title'   => 'История записей',
            'viewing' => [
                'p1' => 'Просмотр записей',
                'p2' => 'through',
                'p3' => 'out of',
            ],
            'table'   => [
                'col1' => 'Дата',
                'col2' => 'Команды игрока',
                'col3' => 'Команды на игрока',
                'col4' => 'Цель',
                'col5' => 'Источник',
                'col6' => 'Сервер',
                'col7' => 'Сообщение',
            ],
        ],
        /**
         * Charts
         */
        'charts'      => [
            'command_overview' => [
                'title' => 'Команды игрока',
                'chart' => [
                    'title'   => 'Список использованных команд данным игроком',
                    'tooltip' => 'Использование',
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
];
