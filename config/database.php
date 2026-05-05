<?php
// Detectar si es localhost o producción
$is_localhost = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === 'localhost:80' ||
    $_SERVER['HTTP_HOST'] === 'localhost:8080' ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

// Configuración según el entorno
if ($is_localhost) {
    // Configuración LOCALHOST
    $host = 'localhost';
  //  $dbname = 'game';
    //$username = 'root';
    //$password = '';
    $dbname = 'u952965051_game';
    $username = 'u952965051_game';
    $password = 'main1001_Game';
} else {
    // Configuración PRODUCCIÓN (Web)
    $host = 'localhost'; // Generalmente localhost en hosting compartido
    $dbname = 'u952965051_game';
    $username = 'u952965051_game';
    $password = 'main1001_Game';
}

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

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255),
    google_id VARCHAR(255) UNIQUE,
    google_picture VARCHAR(500),
    role ENUM('admin','creator','student') DEFAULT 'student',
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
";

$pdo->exec($createTables);

// Agregar columnas faltantes a la tabla users si no existen
try {
    // Verificar y agregar columna 'email' si no existe
    $checkEmail = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($checkEmail->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE AFTER username");
    }
    
    // Verificar y agregar columna 'google_id' si no existe
    $checkGoogleId = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($checkGoogleId->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE AFTER password");
    }
    
    // Verificar y agregar columna 'google_picture' si no existe
    $checkGooglePic = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_picture'");
    if ($checkGooglePic->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_picture VARCHAR(500) AFTER google_id");
    }
    
    // Verificar y agregar columna 'created_at' si no existe
    $checkCreatedAt = $pdo->query("SHOW COLUMNS FROM users LIKE 'created_at'");
    if ($checkCreatedAt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
    
    // Verificar y agregar columna 'last_login' si no existe
    $checkLastLogin = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    if ($checkLastLogin->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL");
    }
    
    // Hacer que la contraseña sea nullable si no lo es
    $checkPassword = $pdo->query("SHOW COLUMNS FROM users WHERE Field='password'");
    $passwordInfo = $checkPassword->fetch(PDO::FETCH_ASSOC);
    if ($passwordInfo && $passwordInfo['Null'] === 'NO') {
        $pdo->exec("ALTER TABLE users MODIFY password VARCHAR(255)");
    }
} catch(PDOException $e) {
    // Si falla, continuamos (puede ser un error de permisos)
}

// Crear usuario administrador por defecto
$adminHash = password_hash('main1001_Domingo', PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role, approved) VALUES ('domingo', 'admin@cerp.local', ?, 'admin', 1)");
$stmt->execute([$adminHash]);
?>