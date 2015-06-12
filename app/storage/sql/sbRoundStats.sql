SELECT
    *
FROM
    tbl_extendedroundstats
WHERE
    server_id = ?
HAVING round_Id = (SELECT
        MAX(round_id)
    FROM
        tbl_extendedroundstats
    WHERE
        server_id = ?)
