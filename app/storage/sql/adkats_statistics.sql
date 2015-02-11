SELECT
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 3
                        OR `command_action` = 4
                        OR `command_action` = 5
                        OR `command_action` = 54)
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageKilled`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 3
                        OR `command_action` = 4
                        OR `command_action` = 5
                        OR `command_action` = 54)
                        AND (`source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageKilled_Admins`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 6)
                        AND (`source_name` <> 'AFKManager')
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageKicked`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 6)
                        AND (`source_name` <> 'AFKManager'
                        AND `source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageKicked_Admins`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 7
                        OR `command_action` = 8
                        OR `command_action` = 72
                        OR `command_action` = 73)
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageBanned`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    (`command_action` = 7
                        OR `command_action` = 8
                        OR `command_action` = 72
                        OR `command_action` = 73)
                        AND (`source_name` <> 'BanEnforcer'
                        AND `source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / :pcount * 100,
            3) AS `PercentageBanned_Admins`,
    ROUND((SELECT
                    COUNT(`ban_id`)
                FROM
                    `adkats_bans`
                WHERE
                    `ban_status` = 'Active'
                        AND `ban_endTime` > UTC_TIMESTAMP()) / :pcount * 100,
            3) AS `PercentageBanned_Active`
FROM
    `tbl_playerdata`
LIMIT 1
