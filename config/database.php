<?php
$host = 'localhost';
$dbname = 'educational_games';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Crear tablas si no existen
$createTables = "
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    author VARCHAR(100) NOT NULL,
    folder_name VARCHAR(100) UNIQUE NOT NULL,
    category VARCHAR(50),
    age_group VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved BOOLEAN DEFAULT FALSE,
    total_votes INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT,
    user_ip VARCHAR(45),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (game_id, user_ip)
);
";

$pdo->exec($createTables);
?>