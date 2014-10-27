ALTER TABLE `tbl_chatlog` ADD INDEX (logDate);
ALTER TABLE `bfadmincp_settings`
CHANGE COLUMN `context` `context` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL;
INSERT INTO `bfadmincp_settings` (`token`, `context`, `description`) VALUES ('CLANNAME', '', 'Set your clan or community name to be appened to the page title.');
UPDATE `bfadmincp_settings` SET `context`='2.0.0-rc.1' WHERE `token` = 'VERSION';
