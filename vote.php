<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$gameId = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

if (!$gameId || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$userIP = getUserIP();

try {
    $stmt = $pdo->prepare("INSERT INTO votes (game_id, user_ip, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
    $stmt->execute([$gameId, $userIP, $rating, $rating]);
    
    updateGameRating($gameId, $pdo);
    
    $stmt = $pdo->prepare("SELECT average_rating, total_votes FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    $gameStats = $stmt->fetch();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Voto registrado exitosamente',
        'new_average' => number_format($gameStats['average_rating'], 1),
        'total_votes' => $gameStats['total_votes']
    ]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al procesar el voto']);
}
?>