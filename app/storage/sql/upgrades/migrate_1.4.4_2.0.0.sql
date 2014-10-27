INSERT INTO `bfadmincp_settings_gameserver` (`server_id`, `rcon_pass_hash`, `name_strip`, `created_at`, `updated_at`)
SELECT `server_id`, `rconHash`, `settings`, `created_at`, `updated_at`
FROM `gamesettings`;

INSERT INTO `bfadmincp_users` (`id`, `username`, `email`, `password`, `confirmation_code`, `confirmed`, `created_at`, `updated_at`, `lastseen_at`)
SELECT `id`, `username`, `email`, `password`, `confirmation_code`, `confirmed`, `created_at`, `updated_at`, UTC_TIMESTAMP()
FROM `users`;

INSERT INTO `bfadmincp_user_preferences` (`user_id`, `timezone`, `created_at`, `updated_at`)
SELECT `id`, `timezone`, `created_at`, `updated_at`
FROM `users`;

INSERT INTO `bfadmincp_assigned_roles` (`user_id`, `role_id`)
SELECT DISTINCT `id`, 9 AS 'role_id'
FROM `bfadmincp_users`
WHERE NOT EXISTS (SELECT `user_id` FROM `bfadmincp_assigned_roles` WHERE `bfadmincp_users`.`id` = `bfadmincp_assigned_roles`.`user_id`)
