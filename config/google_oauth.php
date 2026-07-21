<?php

// 🔐 Cargar configuración privada
$privateConfig = require_once __DIR__ . '/../private/config.php';

// Soportar nueva estructura `['google']` y fallback a la estructura antigua.
$googleConfig = $privateConfig['google'] ?? [];

$googleClientId = trim((string)($googleConfig['client_id'] ?? $privateConfig['google_client_id'] ?? ''));
$googleClientSecret = trim((string)($googleConfig['client_secret'] ?? $privateConfig['google_client_secret'] ?? ''));
$configuredRedirectUri = trim((string)($googleConfig['redirect_uri'] ?? $privateConfig['google_redirect_uri'] ?? ''));

define('GOOGLE_CLIENT_ID', $googleClientId);
define('GOOGLE_CLIENT_SECRET', $googleClientSecret);

if ($configuredRedirectUri !== '') {
    define('GOOGLE_REDIRECT_URI', $configuredRedirectUri);
} else {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptDir = rtrim($scriptDir, '/');
    if ($scriptDir === '.' || $scriptDir === '/') {
        $scriptDir = '';
    }

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
        (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    );

    $scheme = $isHttps ? 'https' : 'http';
    define('GOOGLE_REDIRECT_URI', $scheme . '://' . $host . $scriptDir . '/google_callback.php');
}

// Validación (opcional)
if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    die('Error: Credenciales de Google no configuradas en private/config.php');
}

if (!filter_var(GOOGLE_REDIRECT_URI, FILTER_VALIDATE_URL)) {
    die('Error: GOOGLE_REDIRECT_URI invalida en configuracion de Google OAuth');
}
