<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function assistantRespond(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    assistantRespond(405, ['error' => 'Usa el formulario de recomendaciones para enviar tu consulta.']);
}

$request = json_decode(file_get_contents('php://input', false, null, 0, 2048) ?: '', true);
$message = is_array($request) && isset($request['message']) && is_string($request['message'])
    ? trim($request['message'])
    : '';

$messageCharacterCount = preg_match_all('/./us', $message, $messageCharacters);
if ($message === '' || $messageCharacterCount === false || $messageCharacterCount > 500) {
    assistantRespond(422, ['error' => 'Escribe qué tema o materia quieres practicar (máximo 500 caracteres).']);
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/game_recommendations.php';

try {
    $stmt = $pdo->query(
        'SELECT id, title, description, category, age_group, folder_name, preview_image, average_rating, total_votes
         FROM games
         WHERE approved = 1'
    );
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error al buscar recomendaciones de juegos: ' . $e->getMessage());
    assistantRespond(500, ['error' => 'No se pudieron consultar los juegos. Inténtalo nuevamente.']);
}

$rankedGames = rankGameRecommendations($games, $message);

$availableIcons = getAvailableGameIcons();
$recommendations = [];
foreach (array_slice($rankedGames, 0, 3) as $game) {
    $icon = (string)($game['preview_image'] ?? '');
    $recommendations[] = [
        'id' => (int)$game['id'],
        'title' => (string)$game['title'],
        'description' => (string)($game['description'] ?? ''),
        'category' => (string)($game['category'] ?? ''),
        'age_group' => (string)($game['age_group'] ?? ''),
        'preview_url' => in_array($icon, $availableIcons, true) ? 'images/game-icons/' . $icon : '',
    ];
}

if ($recommendations === []) {
    assistantRespond(200, [
        'reply' => 'Todavía no hay juegos aprobados en el catálogo para recomendar. ¡Vuelve a consultarnos más adelante!',
        'games' => [],
    ]);
}

$reply = '¡Encontramos estas opciones del catálogo que pueden servirte! Si quieres, dinos otra materia, tema o edad para ajustar la recomendación.';
assistantRespond(200, ['reply' => $reply, 'games' => $recommendations]);
