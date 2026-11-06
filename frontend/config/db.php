<?php
/**
 * Conexión única a PostgreSQL mediante PDO.
 *
 * Orden de lectura de la configuración:
 *   1. Variables de entorno (Docker: .env o docker-compose.test.yml).
 *   2. config/config.local.php (hosting tradicional sin variables de entorno).
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $local = __DIR__ . '/config.local.php';
    $file  = is_file($local) ? require $local : [];

    $get = static function (string $key, string $default = '') use ($file): string {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
        return (string) ($file[$key] ?? $default);
    };

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $get('DB_HOST', 'localhost'),
        $get('DB_PORT', '5432'),
        $get('DB_NAME', 'hospital')
    );

    $pdo = new PDO($dsn, $get('DB_USER'), $get('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}
