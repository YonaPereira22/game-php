<?php
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $filename);
    return strtolower($filename);
}

function validateGameFolder($folderPath) {
    if (!file_exists($folderPath . '/index.html')) {
        return false;
    }
    
    $indexContent = file_get_contents($folderPath . '/index.html');
    if (strpos($indexContent, '<script') !== false && 
        (strpos($indexContent, 'eval(') !== false || 
         strpos($indexContent, 'document.write') !== false)) {
        return false;
    }
    
    return true;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function updateGameRating($gameId, $pdo) {
    $stmt = $pdo->prepare("
        UPDATE games 
        SET total_votes = (SELECT COUNT(*) FROM votes WHERE game_id = ?),
            average_rating = (SELECT AVG(rating) FROM votes WHERE game_id = ?)
        WHERE id = ?
    ");
    $stmt->execute([$gameId, $gameId, $gameId]);
}

// ================== FUNCIONES DE AUTENTICACIÓN ==================

/**
 * Verifica si el usuario está logueado
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirige a login si no está logueado
 * @param string $redirect - URL a redirigir después del login
 */
function requireLogin($redirect = null) {
    if (!isLoggedIn()) {
        $url = 'login.php';
        if ($redirect) {
            $url .= '?redirect=' . urlencode($redirect);
        }
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Redirige a login si no es admin
 */
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }
}

/**
 * Redirige a login si no es creador
 */
function requireCreator() {
    requireLogin();
    if (!in_array($_SESSION['role'], ['creator', 'admin'])) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Obtiene la información del usuario actual
 * @param PDO $pdo
 * @return array|null
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obtiene el nombre del usuario actual
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * @return string|null
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Verifica si el usuario actual es admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Verifica si el usuario actual es creador
 * @return bool
 */
function isCreator() {
    return isLoggedIn() && in_array($_SESSION['role'], ['creator', 'admin']);
}

/**
 * Verifica si el usuario está usando Google OAuth
 * @return bool
 */
function isGoogleLogin() {
    return isset($_SESSION['google_picture']) && !empty($_SESSION['google_picture']);
}

/**
 * Obtiene la foto de perfil del usuario actual
 * @return string|null
 */
function getUserProfilePicture() {
    return $_SESSION['google_picture'] ?? null;
}
