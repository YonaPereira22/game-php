-- 05_cargar_datos_users.sql
-- Carga/actualiza usuarios base desde scripts/data/users.sql.

INSERT INTO users (
  id, username, email, password, google_id, google_picture,
  role, approved, created_at, last_login
) VALUES
  (1, 'domingo', NULL, '$2y$10$wNlIUujHofzKJbtYVCOBBuLP1CFE.9itUUANrUB/.yMgqG8aLrXo6', NULL, NULL, 'admin', 1, '2026-04-28 19:17:29', NULL),
  (14, 'creador', NULL, '$2y$10$JxcUgc/fv9C2rKHp//ckD.o7srdfIiOgY34gXATaGypvcaKtt/Bf.', NULL, NULL, 'creator', 1, '2026-04-28 19:17:29', NULL),
  (17, 'usuario', NULL, '$2y$10$199/q27MMnxoXBKlI5usVOQFSVMzPuXw.Pe2HiOZk7DHX8QxrP6RK', NULL, NULL, 'student', 1, '2026-04-28 19:17:29', NULL),
  (125, 'sebastian.alvarez', NULL, '$2y$10$CfiuZcayRElOTw8m/rKCK.iZ7o2aT8vC/.tj6.p03ZCq1kpyoBFL2', NULL, NULL, 'creator', 1, '2026-04-28 19:17:29', NULL),
  (241, 'domingoperez1987', 'domingoperez1987@gmail.com', NULL, '107745109144381411135', 'https://lh3.googleusercontent.com/a/ACg8ocLv78ukzI_WIcF9W4zJ11BmCcmTpATctpCGZAbdMlHIk3Sm_X0XUQ=s96-c', 'student', 1, '2026-04-28 19:32:48', '2026-04-28 19:49:52'),
  (244, 'pyonhatan', 'pyonhatan@gmail.com', NULL, '115891380333378711299', 'https://lh3.googleusercontent.com/a/ACg8ocIdpLdCIyT8J3OfEld5eAtPYO6YqHPNj2IwyS15-2aldOu0ctLo=s96-c', 'student', 1, '2026-04-28 19:33:24', '2026-05-01 15:14:43')
ON DUPLICATE KEY UPDATE
  username = VALUES(username),
  email = VALUES(email),
  password = VALUES(password),
  google_id = VALUES(google_id),
  google_picture = VALUES(google_picture),
  role = VALUES(role),
  approved = VALUES(approved),
  created_at = VALUES(created_at),
  last_login = VALUES(last_login);
