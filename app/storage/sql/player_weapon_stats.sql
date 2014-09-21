SELECT
    `tbl_weapons`.`Friendlyname` AS 'Weapon',
    `tbl_weapons`.`Damagetype` AS 'WeponCat',
    SUM(`tbl_weapons_stats`.`Kills`) AS 'Kills',
    SUM(`tbl_weapons_stats`.`Headshots`) AS 'Headshots',
    SUM(`tbl_weapons_stats`.`Deaths`) AS 'Deaths'
FROM
    `tbl_server_player`
        INNER JOIN
    `tbl_weapons_stats` ON `tbl_weapons_stats`.`StatsID` = `tbl_server_player`.`StatsID`
        INNER JOIN
    `tbl_weapons` ON `tbl_weapons_stats`.`WeaponID` = `tbl_weapons`.`WeaponID`
WHERE
    `tbl_server_player`.`PlayerID` = ?
GROUP BY `tbl_weapons`.`WeaponID`;
