-- 09_modificar_games_preview_image.sql
-- Agrega la columna preview_image a la tabla games para guardar el nombre del archivo de previsualización.

ALTER TABLE games
  ADD COLUMN IF NOT EXISTS preview_image VARCHAR(255) NULL AFTER readme;
