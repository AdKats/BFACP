SELECT DISTINCT
    index_name, column_name
FROM
    INFORMATION_SCHEMA.STATISTICS
WHERE
    (table_schema , table_name) = (? , ?)
        AND index_type = 'FULLTEXT';
