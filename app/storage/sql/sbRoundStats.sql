SELECT
    *
FROM
    tbl_extendedroundstats a
WHERE
    a.server_id = ?
        AND round_id = (SELECT
            MAX(b.round_id)
        FROM
            tbl_extendedroundstats b
        WHERE
            b.server_id = a.server_id)
