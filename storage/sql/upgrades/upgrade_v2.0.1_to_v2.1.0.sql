CREATE TABLE IF NOT EXISTS `bfacp_user_role` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `role_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bfacp_user_role_user_id_foreign` (`user_id`),
  KEY `bfacp_user_role_role_id_foreign` (`role_id`),
  CONSTRAINT `bfacp_user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `bfacp_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `bfacp_user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `bfacp_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO `bfacp_user_role`
  SELECT *
  FROM `bfacp_assigned_roles`;

DROP TABLE IF EXISTS `bfacp_assigned_roles`;

SET FOREIGN_KEY_CHECKS = 1;

RENAME TABLE `bfacp_password_reminders` TO `bfacp_password_resets`;
