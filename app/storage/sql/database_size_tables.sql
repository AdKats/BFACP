SELECT
    table_name AS 'tables',
    (data_length + index_length) AS 'size_of_table',
    round(((data_length + index_length) / 1024 / 1024),
            2) 'size_in_mb',
    round(((data_length + index_length) / 1024 / 1024 / 1024),
            2) 'size_in_gb',
    TABLE_ROWS AS 'rowlength'
FROM
    information_schema.TABLES
WHERE
    table_schema = (SELECT DATABASE())
        AND (table_name LIKE 'adkats_%'
        OR table_name LIKE 'bfadmincp_%'
        OR table_name LIKE 'tbl_%')
ORDER BY 1 ASC;
