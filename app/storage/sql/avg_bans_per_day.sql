SELECT
    FORMAT(AVG(a.total), 0) AS 'AvgBansPerDay'
FROM
    (SELECT
        COUNT(`ban_id`) AS 'total'
    FROM
        `adkats_bans`
    GROUP BY YEAR(`ban_startTime`) , MONTH(`ban_startTime`) , DAY(`ban_startTime`)) AS a
