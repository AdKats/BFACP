UPDATE adkats_records_main tb2
INNER JOIN adkats_bans tb3 ON tb3.latest_record_id != tb2.record_id AND tb3.player_id = tb2.target_id
SET command_action = 72
WHERE tb2.command_type = 9 AND tb2.command_action = 7;

UPDATE adkats_records_main tb2
INNER JOIN adkats_bans tb3 ON tb3.latest_record_id != tb2.record_id AND tb3.player_id = tb2.target_id
SET command_action = 73
WHERE tb2.command_type = 9 AND tb2.command_action = 8;

UPDATE adkats_records_main tb2
INNER JOIN adkats_bans tb3 ON tb3.latest_record_id != tb2.record_id AND tb3.player_id = tb2.target_id
SET command_action = 72
WHERE tb2.command_type = 7 AND tb2.command_action = 7;

UPDATE adkats_records_main tb2
INNER JOIN adkats_bans tb3 ON tb3.latest_record_id != tb2.record_id AND tb3.player_id = tb2.target_id
SET command_action = 73
WHERE tb2.command_type = 8 AND tb2.command_action = 8;
