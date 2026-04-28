<?php

// 🔐 Cargar configuración privada
$privateConfig = require_once __DIR__ . '/../../private/config.php';

// Definir constantes usando config privado
define('GOOGLE_CLIENT_ID', $privateConfig['google_client_id']);
define('GOOGLE_CLIENT_SECRET', $privateConfig['google_client_secret']);

// Detectar entorno (esto lo dejás igual)
$is_localhost = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === 'localhost:80' ||
    $_SERVER['HTTP_HOST'] === 'localhost:8080' ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

if ($is_localhost) {
    define('GOOGLE_REDIRECT_URI', 'http://' . $_SERVER['HTTP_HOST'] . '/game/game-php/google_callback.php');
} else {
    define('GOOGLE_REDIRECT_URI', 'https://' . $_SERVER['HTTP_HOST'] . '/google_callback.php');
}

// Validación (opcional)
if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    die('Error: Credenciales de Google no configuradas en private/config.php');
}