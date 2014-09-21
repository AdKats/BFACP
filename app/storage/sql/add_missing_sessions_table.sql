CREATE TABLE IF NOT EXISTS `bfadmincp_sessions` (
    `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `payload` text COLLATE utf8_unicode_ci NOT NULL,
    `last_activity` int(11) NOT NULL,
    UNIQUE KEY `sessions_id_unique` (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_unicode_ci;
