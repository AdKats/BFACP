SELECT
  c.calendar_datetime                AS SelectedDate,
  DATE(c.calendar_datetime)          AS GameDate,
  HOUR(c.calendar_datetime)          AS hour_of_day,
  IFNULL(FORMAT(m.AvgPlayers, 0), 0) AS 'PlayerAvg'
FROM
  bfacp_calendar c
  LEFT JOIN
  tbl_mapstats m ON (DATE(c.calendar_datetime) = DATE(m.TimeRoundEnd)
                     AND HOUR(c.calendar_datetime) = HOUR(m.TimeRoundEnd))
                    AND m.ServerID = ?
WHERE
  (c.calendar_datetime BETWEEN DATE(DATE_SUB(UTC_TIMESTAMP, INTERVAL 2 WEEK)) AND DATE(UTC_TIMESTAMP()))
GROUP BY GameDate, hour_of_day
