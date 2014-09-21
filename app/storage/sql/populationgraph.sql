SELECT DAY(`TimeRoundEnd`) AS 'Day', HOUR(`TimeRoundEnd`) AS 'Hour', `ServerID`, AVG(`AvgPlayers`) AS 'AvgPlayers', `TimeRoundEnd`
FROM `tbl_mapstats`
WHERE `ServerID` = ? AND `TimeRoundEnd` BETWEEN ? AND ?
GROUP BY DAY(`TimeRoundEnd`), HOUR(`TimeRoundEnd`)
