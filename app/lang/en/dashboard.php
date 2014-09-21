<?php

return array(

    'population' => array(
        'table_header' => array(
            'col1' => 'Server',
            'col2' => 'Online',
            'col3' => 'Map'
        ),
        'total' => ':current out of :max slots',
        'title' => ':game Population'
    ),
    'banfeed' => array(
        'metabans' => array(
            'title' => 'Metabans Feed',
            'table_header' => array(
                'col1' => 'Player',
                'col2' => 'Date',
                'col3' => 'Game',
                'col4' => 'Assessment'
            ),
            'assess_view' => 'View Assessment'
        ),
        'table_header' => array(
            'col1' => 'Player',
            'col2' => 'Issued',
            'col3' => 'Expires'
        ),
        'title' => 'Latest :game Bans'
    ),
    'motd' => 'Message of the Day',
    'bans_issued_yesterday' => '{0} Bans Issued Yesterday|{1} Ban Issued Yesterday| [2,Inf] Bans Issued Yesterday',
    'bans_avg_per_day' => 'Average Bans Issued Per Day',
    'players_online' => '{0} Players Online|{1} Player Online| [2,Inf] Players Online',
);
