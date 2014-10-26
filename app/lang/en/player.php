<?php

return array(

    'profile' => array(
        'basic' => array(
            'id'        => 'ID',
            'game'      => 'Game',
            'eaguid'    => 'EAGUID',
            'pbguid'    => 'PBGUID',
            'ip'        => 'IP',
            'last_seen' => 'Last Seen',
            'country'   => 'Country'
        ),
        'overview' => array(
            'infractions' => array(
                'title' => 'Infractions',
                'table' => array(
                    'col1' => 'Server',
                    'col2' => 'Punishes',
                    'col3' => 'Forgives',
                    'col4' => 'Total'
                ),
                'forgive' => array(
                    'text' => 'Forgive',
                    'help' => 'Enter forgive meessage and how many times to forgive the player',
                    'actions' => array(
                        'cancel' => 'Cancel',
                        'ok' => 'Issue Forgive'
                    )
                ),
                'total' => ':playername has <span class="badge bg-red">:total</span> infraction points. Consisting of <span class="badge bg-marron">:punish</span> punishments and <span class="badge bg-blue">:forgive</span> forgives.',
                'none' => ':playername has no infraction points'
            ),

            'bans' => array(
                'current' => array(
                    'title' => 'Current Ban',
                    'table' => array(
                        'col1' => 'Issued',
                        'col2' => 'Expire',
                        'col3' => 'Server',
                        'col4' => 'Type',
                        'col5' => 'Status',
                        'col6' => 'Reason'
                    ),
                    'none' => ':playername does not have an active ban',
                    'issue' => 'Issue Ban',
                    'modify' => 'Modify Ban'
                ),
                'previous' => array(
                    'title' => 'Ban History',
                    'none' => ':playername does not have any previous bans'
                )
            )
        ),
        'section_titles' => array(
            'basic' => 'Basic Info',
            'stats_summary' => 'Stats Summary',
            'stats_per_server' => 'Stats Per Server',
            'session' => 'Session History',
            'commands_on' => 'Commands issued on :playername',
            'commands_by' => 'Commands issued by :playername',
            'chatlogs' => 'Chatlogs'
        )
    )
);
