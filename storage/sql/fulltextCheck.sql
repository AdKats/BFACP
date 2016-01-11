SELECT DISTINCT index_name,
                  COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS
WHERE (table_schema ,
       TABLE_NAME) = (? ,
                      ?)
  AND index_type = 'FULLTEXT';

