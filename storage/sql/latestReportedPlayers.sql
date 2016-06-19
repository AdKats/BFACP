SELECT t2.*,
       t1.record_message
FROM   adkats_records_main t1
       JOIN (SELECT target_id,
                    target_name,
                    Count(record_id) AS `Total`,
                    Max(record_time) AS `Recent`
             FROM   adkats_records_main
             WHERE  command_type IN ( 18, 20 )
                AND record_time >= Date_sub(UTC_TIMESTAMP(), INTERVAL 2 week)
             GROUP  BY target_id
             HAVING `total` >= 5
             ORDER  BY recent DESC) t2
         ON t1.target_id = t2.target_id
            AND t1.record_time = t2.recent