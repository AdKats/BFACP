SELECT `command_name`,
       DAY(`record_time`) AS 'Day',
       MONTH(`record_time`) AS 'Month',
       YEAR(`record_time`) AS 'Year',
       COUNT(`record_id`) AS 'total'
FROM `adkats_records_main`
INNER JOIN `adkats_commands` ON `adkats_records_main`.`command_type` = `adkats_commands`.`command_id`
WHERE `source_id` = ?
  AND `command_active` = 'Active'
GROUP BY DAY(`record_time`) ,
         MONTH(`record_time`) ,
         YEAR(`record_time`) ,
         `command_type`
ORDER BY `record_id`;

