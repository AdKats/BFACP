SELECT
    table_schema,
    Sum(data_length + index_length) AS 'size_of_table',
    Round(Sum(data_length + index_length) / 1024 / 1024,
            1) 'size_in_mb',
    Round(Sum(data_length + index_length) / 1024 / 1024 / 1024,
            1) 'size_in_gb'
FROM
    information_schema.tables
WHERE
    TABLE_SCHEMA = (SELECT DATABASE())
GROUP BY table_schema;
