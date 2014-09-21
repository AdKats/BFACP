SELECT
    tbl_server.ServerName,
    tbl_sessions.SessionID,
    tbl_sessions.StartTime,
    tbl_sessions.EndTime,
    tbl_sessions.Score,
    tbl_sessions.Kills,
    tbl_sessions.Headshots,
    tbl_sessions.Deaths,
    tbl_sessions.TKs,
    tbl_sessions.Suicide,
    tbl_sessions.RoundCount,
    tbl_sessions.Playtime,
    tbl_sessions.Killstreak,
    tbl_sessions.Deathstreak,
    tbl_sessions.HighScore,
    tbl_sessions.Wins,
    tbl_sessions.Losses
FROM
    tbl_server_player
        INNER JOIN
    tbl_server ON tbl_server_player.ServerID = tbl_server.ServerID
        INNER JOIN
    tbl_sessions ON tbl_server_player.StatsID = tbl_sessions.StatsID
WHERE
    tbl_server_player.PlayerID = ?
ORDER BY tbl_sessions.SessionID DESC;
