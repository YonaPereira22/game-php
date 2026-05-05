-- 04_cargar_datos_games.sql
-- Carga/actualiza los registros de juegos desde scripts/data/games.sql.

INSERT INTO games (
  id, title, description, author, folder_name, category, age_group,
  created_at, approved, total_votes, average_rating, github_link
) VALUES
  (1, 'Adivina el Numero', 'Juego simple para adivinar un numero del 1 al 10', 'Admin', 'adivina-numero', 'Logica', '6-8 anos', '2025-06-24 00:13:52', 1, 1, 5.00, NULL),
  (2, 'Memoria ASCII', 'Juego de memoria donde emparejas letras con sus codigos ASCII.', 'Agustin Munoz', 'ascii-memory', 'Codigos de caracteres', '9-12 anos', '2025-06-24 00:26:50', 1, 1, 4.00, NULL),
  (3, 'Codigo Perdido', 'Juego narrativo que combina una historia de recuperacion de memoria con desafios para aprender Python.', 'Sofia Rodriguez', 'codigo-perdido', 'Python', '9-12 anos', '2025-06-24 00:26:50', 1, 0, 0.00, NULL),
  (4, 'Juego del Rosco', 'Juego de preguntas tipo rosco para poner a prueba tus conocimientos de programacion.', 'Domingo Perez', 'rosco', 'Programacion', '9-12 anos', '2025-06-24 00:26:50', 1, 0, 0.00, NULL),
  (5, 'Snake Programer', 'En este juego, controlas una serpiente que consume tokens de programacion en el tablero para formar codigo.', 'Sol Mendez', 'snake', 'Programacion', '9-12 anos', '2025-06-24 00:26:50', 1, 1, 3.00, NULL),
  (6, 'PlayIA', 'Juego creado para el Taller de IA 2025', 'Informatica 2do - 2025', 'playia-1760618248', 'Ciencias', '13-16 anos', '2025-10-16 12:37:28', 1, 0, 0.00, 'https://domingo1987.github.io/PlayIA')
ON DUPLICATE KEY UPDATE
  title = VALUES(title),
  description = VALUES(description),
  author = VALUES(author),
  folder_name = VALUES(folder_name),
  category = VALUES(category),
  age_group = VALUES(age_group),
  created_at = VALUES(created_at),
  approved = VALUES(approved),
  total_votes = VALUES(total_votes),
  average_rating = VALUES(average_rating),
  github_link = VALUES(github_link);
