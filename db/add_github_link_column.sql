-- Migración para agregar el campo github_link a la tabla games
-- Este script agrega la columna para almacenar enlaces de GitHub Pages

USE game;

-- Agregar columna github_link a la tabla games
ALTER TABLE games 
ADD COLUMN github_link VARCHAR(500) DEFAULT NULL AFTER folder_name;

-- Opcional: Agregar índice para búsquedas más rápidas
CREATE INDEX idx_github_link ON games(github_link);
