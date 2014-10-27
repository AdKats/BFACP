ALTER TABLE `tbl_chatlog` ADD INDEX (logDate);
ALTER TABLE `bfadmincp_settings`
CHANGE COLUMN `context` `context` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
INSERT INTO `bfadmincp_settings` (`token`, `context`) VALUES ('CLANNAME', '');
UPDATE `bfadmincp_settings` SET `context`='2.0.0-rc.1' WHERE `token` = 'VERSION';
