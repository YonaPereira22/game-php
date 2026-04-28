<?php
session_start();
require_once 'config/database.php';
require_once 'config/google_oauth.php';
require_once 'includes/functions.php';

// Verificar CSRF token
if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
    die('Error de seguridad: estado inválido');
}

// Verificar si hay código de autorización
if (!isset($_GET['code'])) {
    die('Error: No se recibió código de autorización');
}

$code = $_GET['code'];

// Intercambiar código por token de acceso
$tokenRequest = [
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => GOOGLE_REDIRECT_URI
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenRequest));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die('Error al intercambiar código por token');
}

$tokenData = json_decode($response, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    die('Error: No se recibió token de acceso');
}

// Obtener información del usuario de Google
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$userInfo = json_decode($response, true);

if (!$userInfo || !isset($userInfo['id'], $userInfo['email'])) {
    die('Error: No se pudo obtener información del usuario');
}

$googleId = $userInfo['id'];
$email = $userInfo['email'];
$name = $userInfo['name'] ?? substr($email, 0, strpos($email, '@'));
$picture = $userInfo['picture'] ?? null;

try {
    // Buscar usuario existente por Google ID
    $stmt = $pdo->prepare('SELECT id, username, role, approved FROM users WHERE google_id = ?');
    $stmt->execute([$googleId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Usuario existente - actualizar último acceso y foto
        $updateStmt = $pdo->prepare('
            UPDATE users 
            SET last_login = NOW(), google_picture = ?
            WHERE id = ?
        ');
        $updateStmt->execute([$picture, $user['id']]);
        $userId = $user['id'];
    } else {
        // Buscar por email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            // Usuario existe con email, vincular Google ID
            $updateStmt = $pdo->prepare('
                UPDATE users 
                SET google_id = ?, google_picture = ?, last_login = NOW()
                WHERE id = ?
            ');
            $updateStmt->execute([$googleId, $picture, $existingUser['id']]);
            $userId = $existingUser['id'];
            $user = $existingUser;
        } else {
            // Crear nuevo usuario
            // Generar username único desde el email
            $baseUsername = sanitizeInput(explode('@', $email)[0]);
            $username = $baseUsername;
            $counter = 1;
            
            while (true) {
                $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $stmt->execute([$username]);
                if (!$stmt->fetch()) {
                    break;
                }
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            // Los usuarios de Google se crean como estudiantes y aprobados automáticamente
            $insertStmt = $pdo->prepare('
                INSERT INTO users (username, email, google_id, google_picture, role, approved, created_at, last_login)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ');
            $insertStmt->execute([$username, $email, $googleId, $picture, 'student', true]);
            $userId = $pdo->lastInsertId();
            
            $user = [
                'id' => $userId,
                'username' => $username,
                'role' => 'student',
                'approved' => true
            ];
        }
    }
    
    // Verificar si está aprobado
    if (!$user['approved']) {
        $_SESSION['message'] = 'Tu cuenta está pendiente de aprobación. Por favor, espera la confirmación del administrador.';
        $_SESSION['messageType'] = 'error';
        header('Location: login.php');
        exit;
    }
    
    // Crear sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['google_picture'] = $picture;
    
    // Limpiar token y estado
    unset($_SESSION['oauth_state']);
    
    // Redirigir al índice
    header('Location: index.php');
    exit;
    
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
}
