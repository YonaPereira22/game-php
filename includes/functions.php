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
?>