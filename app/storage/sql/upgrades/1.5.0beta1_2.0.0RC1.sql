ALTER TABLE `tbl_chatlog` ADD INDEX (logDate);
ALTER TABLE `bfadmincp_settings`
CHANGE COLUMN `context` `context` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
