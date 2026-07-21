<?php

declare(strict_types=1);

$configPath = dirname(__DIR__) . '/private/config.php';

if (is_readable($configPath)) {
    $config = require $configPath;
} else {
    // Intentar cargar ejemplo de configuración como fallback para desarrollo
    $examplePath = dirname(__DIR__) . '/private/config.php.example';
    if (is_readable($examplePath)) {
        error_log('Archivo privado de configuración no encontrado: usando ejemplo (private/config.php.example)');
        $config = require $examplePath;
    } else {
        // Intentar extraer credenciales mínimas desde .env en el repo (solo para desarrollo)
        $envPath = dirname(__DIR__) . '/.env';
        if (is_readable($envPath)) {
            error_log('Archivo privado de configuración no encontrado: cargando variables desde .env como fallback.');
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $env = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v);
            }
            $config = [
                'app' => [
                    'environment' => $env['APP_ENV'] ?? 'development',
                    'version' => $env['APP_VERSION'] ?? '1.0.0',
                ],
                'database' => [
                    'host' => $env['DB_HOST'] ?? 'localhost',
                    'name' => $env['DB_NAME'] ?? '',
                    'user' => $env['DB_USER'] ?? '',
                    'password' => $env['DB_PASS'] ?? '',
                    'charset' => $env['DB_CHARSET'] ?? 'utf8mb4',
                ],
                'upload_types' => $env['UPLOAD_TYPES'] ?? 'ambos',
            ];
        } else {
            error_log('No se pudo leer el archivo privado de configuración ni su ejemplo.');
            http_response_code(500);
            exit('Error de configuración del servidor.');
        }
    }
}

if (!is_array($config)) {
    error_log('La configuración cargada no devolvió un array.');
    http_response_code(500);
    exit('Error de configuración del servidor.');
}
