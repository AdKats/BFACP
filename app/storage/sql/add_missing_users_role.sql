INSERT INTO bfadmincp_assigned_roles (user_id, role_id)
SELECT DISTINCT id, 9 AS 'role_id'
FROM bfadmincp_users
WHERE NOT EXISTS (SELECT user_id FROM bfadmincp_assigned_roles WHERE bfadmincp_users.id = bfadmincp_assigned_roles.user_id)
