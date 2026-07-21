<?php

declare(strict_types=1);

// Comprobar varias rutas posibles para private/config.php según la estructura de despliegue.
$possibleConfigPaths = [
    dirname(__DIR__) . '/private/config.php',          // config/bootstrap.php
    dirname(__DIR__, 2) . '/private/config.php',       // public_html/config/bootstrap.php
    dirname(__DIR__, 3) . '/private/config.php',       // otro nivel adicional si aplica
];

$configPath = null;
foreach ($possibleConfigPaths as $path) {
    if (is_readable($path)) {
        $configPath = $path;
        break;
    }
}

if ($configPath === null) {
    error_log('No se pudo leer private/config.php. Rutas probadas: ' . implode(', ', $possibleConfigPaths));
    http_response_code(500);
    exit('Error de configuración del servidor.');
}

$config = require $configPath;

if (!is_array($config)) {
    error_log('private/config.php debe devolver un array. Archivo leído: ' . $configPath);
    http_response_code(500);
    exit('Error de configuración del servidor.');
}
