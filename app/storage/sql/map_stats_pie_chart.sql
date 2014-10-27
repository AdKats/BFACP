SELECT
    MapName, COUNT(ID) AS 'Total'
FROM
    tbl_mapstats
WHERE
    MapName != ''
        AND TimeMapLoad >= DATE_SUB(UTC_TIMESTAMP(),
        INTERVAL 1 WEEK)
        AND TimeRoundStarted != '0001-01-01 00:00:00'
        AND TimeRoundEnd != '0001-01-01 00:00:00'
        AND TimeMapLoad != '0001-01-01 00:00:00'
        AND ServerID = ?
GROUP BY MapName
ORDER BY 2 DESC
