SELECT command_name AS 'label',
       SUM(command_count) AS 'value'
FROM (
        (SELECT tb2.command_name,
                COUNT(tb1.record_id) AS command_count
         FROM adkats_records_main tb1
         RIGHT JOIN adkats_commands tb2 ON tb1.command_type = tb2.command_id
         WHERE tb1.source_id = ?
         GROUP BY tb1.command_type)
      UNION ALL
        (SELECT command_name,
                0 AS command_count
         FROM adkats_commands
         WHERE adkats_commands.command_playerInteraction = 1
           AND command_active = 'Active')) x
GROUP BY command_name HAVING `value` > 0