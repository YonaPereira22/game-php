-- 01_crear_estructura_base.sql
-- Crea la base y las tablas iniciales del proyecto.
-- Ejecutar dentro de la base ya seleccionada en el hosting (por ejemplo: u952965051_game).

CREATE TABLE IF NOT EXISTS games (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  author VARCHAR(100) NOT NULL,
  folder_name VARCHAR(100) NOT NULL,
  category VARCHAR(50),
  age_group VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  approved BOOLEAN DEFAULT FALSE,
  total_votes INT DEFAULT 0,
  average_rating DECIMAL(3,2) DEFAULT 0.00,
  UNIQUE KEY uq_games_folder_name (folder_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','creator','student') DEFAULT 'student',
  approved BOOLEAN DEFAULT FALSE,
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT NOT NULL,
  user_ip VARCHAR(45),
  rating INT,
  voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_votes_rating CHECK (rating >= 1 AND rating <= 5),
  CONSTRAINT fk_votes_game FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
  UNIQUE KEY uq_votes_game_ip (game_id, user_ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
