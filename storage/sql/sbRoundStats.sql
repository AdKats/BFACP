SELECT *
FROM tbl_extendedroundstats
WHERE server_id = ?
  AND round_id =
    (SELECT MAX(round_id)
     FROM tbl_extendedroundstats
     WHERE server_id = ?)