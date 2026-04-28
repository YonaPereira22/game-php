<?php
session_start();

// Cargar configuración de Google OAuth
require_once 'config/google_oauth.php';

// Generar CSRF token
$_SESSION['oauth_state'] = bin2hex(random_bytes(16));

// Construir URL de autorización de Google
$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $_SESSION['oauth_state'],
    'prompt' => 'consent'
];

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $authUrl);
exit;
