<?php
declare(strict_types=1);

function normalizeRecommendationText(string $text): string
{
    $text = strtr(strtolower($text), [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
    ]);

    return trim(preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? '');
}

function recommendationAgeRange(string $ageGroup): ?array
{
    if (preg_match('/(\d{1,2})\s*\+/u', $ageGroup, $matches)) {
        return [(int)$matches[1], 99];
    }

    if (preg_match('/(\d{1,2})\s*[-–a]\s*(\d{1,2})/u', $ageGroup, $matches)) {
        return [(int)$matches[1], (int)$matches[2]];
    }

    $ageGroup = normalizeRecommendationText($ageGroup);
    if ($ageGroup === '' || strpos($ageGroup, 'todas las edades') !== false) {
        return null;
    }

    if (preg_match('/(\d{1,2})/u', $ageGroup, $matches)) {
        return [(int)$matches[1], (int)$matches[1]];
    }

    return null;
}

function rankGameRecommendations(array $games, string $message): array
{
    $normalizedMessage = normalizeRecommendationText($message);
    $requestedAllAges = strpos($normalizedMessage, 'todas las edades') !== false
        || strpos($normalizedMessage, 'cualquier edad') !== false;
    $ageRange = null;

    if (preg_match('/(?:para|de|tengo|tienen|edad)\s+(\d{1,2})\s*(?:anos?)?/iu', $message, $ageMatch)) {
        $requestedAge = (int)$ageMatch[1];
        if ($requestedAge >= 3 && $requestedAge <= 99) {
            $ageRange = [$requestedAge, $requestedAge];
        }
    }

    $stopWords = [
        'algo', 'algun', 'alguna', 'aprender', 'ayuda', 'busco', 'como', 'con', 'cual',
        'cuales', 'de', 'del', 'donde', 'edad', 'el', 'ella', 'en', 'es', 'favor', 'gustaria',
        'hacer', 'juego', 'juegos', 'la', 'las', 'lo', 'los', 'mas', 'me', 'mi', 'necesito',
        'para', 'por', 'puedes', 'quiero', 'recomendar', 'recomiendame', 'recomiendas',
        'ano', 'anos', 'cualquier', 'edades', 'sobre', 'tengo', 'tema', 'todas', 'un', 'una', 'uno', 'y',
    ];
    $keywords = array_values(array_unique(array_filter(
        explode(' ', $normalizedMessage),
        static function (string $word) use ($stopWords): bool {
            return strlen($word) > 2 && !in_array($word, $stopWords, true) && !preg_match('/^\d+$/', $word);
        }
    )));

    foreach ($games as &$game) {
        $category = normalizeRecommendationText((string)($game['category'] ?? ''));
        $title = normalizeRecommendationText((string)($game['title'] ?? ''));
        $description = normalizeRecommendationText((string)($game['description'] ?? ''));
        $score = min((float)($game['average_rating'] ?? 0), 5) * 0.1
            + min((int)($game['total_votes'] ?? 0), 100) * 0.001;

        foreach ($keywords as $keyword) {
            if (strpos($category, $keyword) !== false) {
                $score += 6;
            }
            if (strpos($title, $keyword) !== false) {
                $score += 4;
            }
            if (strpos($description, $keyword) !== false) {
                $score += 1;
            }
        }

        if ($requestedAllAges) {
            $gameAgeRange = recommendationAgeRange((string)($game['age_group'] ?? ''));
            $score += $gameAgeRange === null ? 5 : -2;
        } elseif ($ageRange !== null) {
            $gameAgeRange = recommendationAgeRange((string)($game['age_group'] ?? ''));
            if ($gameAgeRange === null) {
                $score += 1;
            } elseif ($gameAgeRange[0] <= $ageRange[1] && $gameAgeRange[1] >= $ageRange[0]) {
                $score += 4;
            } else {
                $score -= 3;
            }
        }

        $game['recommendation_score'] = $score;
    }
    unset($game);

    usort($games, static function (array $left, array $right): int {
        $scoreOrder = $right['recommendation_score'] <=> $left['recommendation_score'];
        if ($scoreOrder !== 0) {
            return $scoreOrder;
        }

        $ratingOrder = (float)($right['average_rating'] ?? 0) <=> (float)($left['average_rating'] ?? 0);
        if ($ratingOrder !== 0) {
            return $ratingOrder;
        }

        return (int)($right['total_votes'] ?? 0) <=> (int)($left['total_votes'] ?? 0);
    });

    return $games;
}
