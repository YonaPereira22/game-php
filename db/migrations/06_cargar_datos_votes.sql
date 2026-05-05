-- 06_cargar_datos_votes.sql
-- Carga/actualiza votos base desde scripts/data/votes.sql.

INSERT INTO votes (id, game_id, user_ip, rating, voted_at) VALUES
  (1, 2, '200.125.57.30', 4, '2025-10-16 11:46:04'),
  (2, 1, '200.125.57.30', 5, '2026-04-21 16:50:28'),
  (3, 5, '200.125.57.30', 3, '2026-04-21 17:05:07')
ON DUPLICATE KEY UPDATE
  game_id = VALUES(game_id),
  user_ip = VALUES(user_ip),
  rating = VALUES(rating),
  voted_at = VALUES(voted_at);
