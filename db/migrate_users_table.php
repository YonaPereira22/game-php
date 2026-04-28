<?php
/**
 * Script de migración para actualizar la tabla 'users'
 * Ejecutar una sola vez para agregar los nuevos campos de Google OAuth
 * 
 * Uso: php db/migrate_users_table.php
 */

require_once dirname(__DIR__) . '/config/database.php';

try {
    echo "Iniciando migración de la tabla 'users'...\n\n";
    
    // Verificar si la columna 'email' existe
    $checkEmail = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($checkEmail->rowCount() === 0) {
        echo "Agregando columna 'email'...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE NOT NULL AFTER username");
    } else {
        echo "✓ Columna 'email' ya existe\n";
    }
    
    // Verificar si la columna 'password' permite NULL
    $checkPassword = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'");
    $result = $checkPassword->fetch(PDO::FETCH_ASSOC);
    if ($result['Null'] === 'NO') {
        echo "Actualizando columna 'password' para permitir NULL...\n";
        $pdo->exec("ALTER TABLE users MODIFY password VARCHAR(255)");
    } else {
        echo "✓ Columna 'password' ya permite NULL\n";
    }
    
    // Verificar si la columna 'google_id' existe
    $checkGoogleId = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_id'");
    if ($checkGoogleId->rowCount() === 0) {
        echo "Agregando columna 'google_id'...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE AFTER password");
    } else {
        echo "✓ Columna 'google_id' ya existe\n";
    }
    
    // Verificar si la columna 'google_picture' existe
    $checkPicture = $pdo->query("SHOW COLUMNS FROM users LIKE 'google_picture'");
    if ($checkPicture->rowCount() === 0) {
        echo "Agregando columna 'google_picture'...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN google_picture VARCHAR(500) AFTER google_id");
    } else {
        echo "✓ Columna 'google_picture' ya existe\n";
    }
    
    // Verificar si la columna 'created_at' existe
    $checkCreatedAt = $pdo->query("SHOW COLUMNS FROM users LIKE 'created_at'");
    if ($checkCreatedAt->rowCount() === 0) {
        echo "Agregando columna 'created_at'...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    } else {
        echo "✓ Columna 'created_at' ya existe\n";
    }
    
    // Verificar si la columna 'last_login' existe
    $checkLastLogin = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    if ($checkLastLogin->rowCount() === 0) {
        echo "Agregando columna 'last_login'...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL");
    } else {
        echo "✓ Columna 'last_login' ya existe\n";
    }
    
    echo "\n✓ Migración completada exitosamente\n";
    
} catch (PDOException $e) {
    echo "❌ Error en la migración: " . $e->getMessage() . "\n";
    exit(1);
}
