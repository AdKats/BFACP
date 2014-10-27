SELECT
    `id`, `username`, `lastseen_at`
FROM
    `bfadmincp_users`
WHERE
    `lastseen_at` >= DATE_SUB(UTC_TIMESTAMP(),
        INTERVAL 15 MINUTE);
