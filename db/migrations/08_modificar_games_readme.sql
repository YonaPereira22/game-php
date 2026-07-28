-- 08_modificar_games_readme.sql
-- Agrega la columna readme a la tabla games para almacenar documentación por juego.

ALTER TABLE games
  ADD COLUMN IF NOT EXISTS readme TEXT NULL AFTER github_link;
