SELECT
    tpd.PlayerID,
    tpd.SoldierName,
    SUM(tps.Score) AS 'Score',
    SUM(tps.Kills) AS 'Kills',
    SUM(tps.Headshots) AS 'Headshots',
    SUM(tps.Deaths) AS 'Deaths',
    SUM(tps.Suicide) AS 'Suicides',
    SUM(tps.TKs) AS 'TKs',
    SUM(tps.Playtime) AS 'Playtime',
    SUM(tps.Rounds) AS 'Rounds',
    SUM(tps.Wins) AS 'Wins',
    SUM(tps.Losses) AS 'Losses'
FROM
    tbl_playerdata tpd
        INNER JOIN
    tbl_server_player tsp ON tsp.PlayerID = tpd.PlayerID
        INNER JOIN
    tbl_playerstats tps ON tps.StatsID = tsp.StatsID
WHERE
    tpd.GameID = :game
GROUP BY tpd.PlayerID
ORDER BY 3 DESC
LIMIT 50
