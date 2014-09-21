SELECT
    SUM(`tbl_playerstats`.`Score`) AS 'Score',
    SUM(`tbl_playerstats`.`Kills`) AS 'Kills',
    SUM(`tbl_playerstats`.`Headshots`) AS 'Headshots',
    SUM(`tbl_playerstats`.`Deaths`) AS 'Deaths',
    SUM(`tbl_playerstats`.`Suicide`) AS 'Suicide',
    SUM(`tbl_playerstats`.`TKs`) AS 'TKs',
    SUM(`tbl_playerstats`.`Playtime`) AS 'Playtime',
    SUM(`tbl_playerstats`.`Wins`) AS 'Wins',
    SUM(`tbl_playerstats`.`Losses`) AS 'Losses'
FROM
    `tbl_server_player`
        INNER JOIN
    `tbl_playerstats` ON `tbl_playerstats`.`StatsID` = `tbl_server_player`.`StatsID`
WHERE
    `tbl_server_player`.`PlayerID` = ?;
