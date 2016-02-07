CREATE TABLE `bfacp_user_role` (
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

INSERT INTO `bfacp_user_role`
  SELECT *
  FROM `bfacp_assigned_roles`;

DROP TABLE IF EXISTS `bfacp_assigned_roles`;
