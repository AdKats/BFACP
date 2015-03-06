SELECT
    player_id, EAGUID, GameID, SoldierName
FROM
    adkats_usersoldiers
        INNER JOIN
    adkats_users ON adkats_usersoldiers.user_id = adkats_users.user_id
        INNER JOIN
    adkats_roles ON adkats_users.user_role = adkats_roles.role_id
        INNER JOIN
    tbl_playerdata ON adkats_usersoldiers.player_id = tbl_playerdata.PlayerID
        AND tbl_playerdata.GameID = ?
WHERE
    EXISTS( SELECT
            adkats_rolecommands.role_id
        FROM
            adkats_rolecommands
                INNER JOIN
            adkats_commands ON adkats_rolecommands.command_id = adkats_commands.command_id
        WHERE
            adkats_commands.command_playerInteraction = 1
                AND adkats_rolecommands.role_id = adkats_users.user_role
        GROUP BY role_id)
