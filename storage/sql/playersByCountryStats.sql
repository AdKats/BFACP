SELECT
  z.CC,
  z.Cur,
  z.Past,
  z.Rate,
  IF(z.Diff < 0, TRUE, FALSE) AS ISNEG
FROM
  (SELECT
     *,
     CONCAT(FORMAT((1 - a.Past / a.Cur) * 100, 2), '%') AS Rate,
     ((1 - a.Past / a.Cur))                             AS Diff
   FROM
     (SELECT
        UPPER(tb1.CountryCode) AS CC,
        COUNT(tb1.PlayerID)    AS Cur,
        c.total                AS Past
      FROM
        tbl_playerdata tb1
        INNER JOIN (SELECT
                      COUNT(tb2.PlayerID)    AS total,
                      UPPER(tb2.CountryCode) AS CountryCode
                    FROM
                      tbl_playerdata tb2
                    WHERE
                      tb2.PlayerID IN (SELECT DISTINCT tbl_server_player.PlayerID
                                       FROM
                                         tbl_server_player
                                       WHERE
                                         tbl_server_player.StatsID IN (SELECT StatsID
                                                                       FROM
                                                                         tbl_playerstats
                                                                       WHERE
                                                                         LastSeenOnServer BETWEEN DATE_SUB(
                                                                             UTC_TIMESTAMP, INTERVAL 2
                                                                             DAY) AND DATE_SUB(UTC_TIMESTAMP, INTERVAL 1
                                                                                               DAY)))
                    GROUP BY CountryCode) c ON c.CountryCode = tb1.CountryCode
                                               AND c.CountryCode NOT IN ('', '--')
      WHERE
        tb1.CountryCode NOT IN ('', '--')
        AND tb1.PlayerID IN (SELECT DISTINCT tbl_server_player.PlayerID
                             FROM
                               tbl_server_player
                             WHERE
                               tbl_server_player.StatsID IN (SELECT StatsID
                                                             FROM
                                                               tbl_playerstats
                                                             WHERE
                                                               LastSeenOnServer >=
                                                               DATE_SUB(UTC_TIMESTAMP, INTERVAL 1 DAY)))
      GROUP BY CC) AS a) AS z
