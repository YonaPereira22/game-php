-- 03_modificar_games_github_link.sql
-- Agrega el campo para publicar enlaces de GitHub Pages en juegos.

ALTER TABLE games
  ADD COLUMN IF NOT EXISTS github_link VARCHAR(500) NULL AFTER average_rating;

ALTER TABLE games
  ADD UNIQUE KEY IF NOT EXISTS uq_games_github_link (github_link);
