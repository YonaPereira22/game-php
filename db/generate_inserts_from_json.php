<?php
// Script to generate SQL insert statements from games/juegos.json

$jsonPath = __DIR__ . '/../games/juegos.json';
if (!file_exists($jsonPath)) {
    fwrite(STDERR, "juegos.json not found\n");
    exit(1);
}
$data = json_decode(file_get_contents($jsonPath), true);
if (!$data) {
    fwrite(STDERR, "Failed to parse juegos.json\n");
    exit(1);
}
$defaultCategory = 'Lógica';
$defaultAge = '9-12 años';
foreach ($data as $folder => $info) {
    $folderDir = __DIR__ . '/../games/' . $folder;
    if (is_dir($folderDir) && file_exists($folderDir . '/index.html')) {
        $title = addslashes($info['nombre'] ?? $folder);
        $description = addslashes($info['descripcion'] ?? '');
        $author = addslashes($info['autor'] ?? 'Desconocido');
        $category = addslashes($info['temas'][0] ?? $defaultCategory);
        echo "INSERT INTO games (title, description, author, folder_name, category, age_group, approved) VALUES ('$title', '$description', '$author', '$folder', '$category', '$defaultAge', 1);\n";
    }
}
?>
