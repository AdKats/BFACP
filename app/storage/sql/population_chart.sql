SELECT
    FORMAT(AVG(`AvgPlayers`), 0) AS 'pcount',
    YEAR(`TimeRoundEnd`) AS 'Year',
    MONTH(`TimeRoundEnd`) AS 'Month',
    DAY(`TimeRoundEnd`) AS 'Day',
    HOUR(`TimeRoundEnd`) AS 'Hour'
FROM
    `tbl_mapstats`
WHERE
    DATE_SUB(`TimeRoundEnd`,
        INTERVAL 1 HOUR)
        AND `ServerID` = ?
        AND `TimeRoundEnd` >= UTC_TIMESTAMP() - INTERVAL 1 MONTH
GROUP BY YEAR(`TimeRoundEnd`) , MONTH(`TimeRoundEnd`) , DAY(`TimeRoundEnd`) , HOUR(`TimeRoundEnd`)
