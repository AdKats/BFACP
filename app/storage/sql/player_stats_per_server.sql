SELECT
    `tbl_playerstats`.`Score`,
    `tbl_playerstats`.`Kills`,
    `tbl_playerstats`.`Headshots`,
    `tbl_playerstats`.`Deaths`,
    `tbl_playerstats`.`Suicide`,
    `tbl_playerstats`.`Playtime`,
    `tbl_playerstats`.`Rounds`,
    `tbl_playerstats`.`FirstSeenOnServer`,
    `tbl_playerstats`.`LastSeenOnServer`,
    UNIX_TIMESTAMP(`tbl_playerstats`.`FirstSeenOnServer`) AS FirstSeenOnServerUnix,
    UNIX_TIMESTAMP(`tbl_playerstats`.`LastSeenOnServer`) AS LastSeenOnServerUnix,
    `tbl_playerstats`.`Killstreak`,
    `tbl_playerstats`.`Deathstreak`,
    `tbl_playerstats`.`HighScore`,
    `tbl_playerstats`.`Wins`,
    `tbl_playerstats`.`Losses`,
    `tbl_server`.`ServerName`
FROM
    tbl_server_player
        INNER JOIN
    tbl_playerstats ON tbl_server_player.StatsID = tbl_playerstats.StatsID
        INNER JOIN
    tbl_server ON tbl_server_player.ServerID = tbl_server.ServerID
        AND tbl_server.ConnectionState = 'on'
WHERE
    tbl_server_player.PlayerID = ?
ORDER BY tbl_server_player.StatsID
