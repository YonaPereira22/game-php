<?php

declare(strict_types=1);

$configPath = dirname(__DIR__) . '/private/config.php';

if (!is_readable($configPath)) {
    error_log('No se pudo leer el archivo privado de configuración.');
    http_response_code(500);
    exit('Error de configuración del servidor.');
}

$config = require $configPath;

if (!is_array($config)) {
    error_log('El archivo privado de configuración no devolvió un array.');
    http_response_code(500);
    exit('Error de configuración del servidor.');
}
