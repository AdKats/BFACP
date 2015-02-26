SELECT
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (3 , 4, 5, 54)
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageKilled`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (3 , 4, 5, 54)
                        AND (`source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageKilled_Admins`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (6)
                        AND (`source_name` <> 'AFKManager')
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageKicked`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (6)
                        AND (`source_name` <> 'AFKManager'
                        AND `source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageKicked_Admins`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (7 , 8, 72, 73)
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageBanned`,
    ROUND((SELECT DISTINCT
                    COUNT(`target_id`)
                FROM
                    `adkats_records_main`
                WHERE
                    `command_action` IN (7 , 8, 72, 73)
                        AND (`source_name` <> 'BanEnforcer'
                        AND `source_name` <> 'AutoAdmin')
                        AND `target_id` IS NOT NULL) / ? * 100,
            3) AS `PercentageBanned_Admins`,
    ROUND((SELECT
                    COUNT(`ban_id`)
                FROM
                    `adkats_bans`
                WHERE
                    `ban_status` = 'Active'
                        AND `ban_endTime` > UTC_TIMESTAMP()) / ? * 100,
            3) AS `PercentageBanned_Active`
FROM
    `tbl_playerdata`
LIMIT 1
