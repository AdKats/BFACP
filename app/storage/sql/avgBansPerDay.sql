SELECT FORMAT(AVG(a.total), 0) AS 'total'
FROM
  (SELECT COUNT(`ban_id`) AS 'total'
   FROM `adkats_bans`
   WHERE `ban_startTime` >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 MONTH)
   GROUP BY YEAR(`ban_startTime`) ,
            MONTH(`ban_startTime`) , DAY(`ban_startTime`)) AS a