<?php
// Pruebas básicas que se ejecutan dentro de docker-compose.test.yml.
// Termina con código 0 si todo pasa y 1 si algo falla.
require '/var/www/html/config/db.php';

$fallos = 0;
function check(string $nombre, bool $ok): void
{
    global $fallos;
    echo ($ok ? '[OK]   ' : '[FALLO] ') . $nombre . PHP_EOL;
    if (!$ok) {
        $fallos++;
    }
}

// 1. PHP 8.2 y extensión PDO PostgreSQL
check('PHP es 8.2', str_starts_with(PHP_VERSION, '8.2'));
check('Extensión pdo_pgsql cargada', extension_loaded('pdo_pgsql'));

// 2. Conexión a la BD por DNS interno (DB_HOST=db)
try {
    $pdo = db();
    check('Conexión PDO a ' . getenv('DB_HOST'), true);

    $total = (int) $pdo->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    check('Tabla pacientes con datos iniciales', $total >= 2);

    $pdo->prepare('INSERT INTO pacientes (rut, nombre, apellido) VALUES (?, ?, ?)')
        ->execute(['99999999-9', 'Test', 'Automático']);
    $nuevo = (int) $pdo->query('SELECT COUNT(*) FROM pacientes')->fetchColumn();
    check('Insertar paciente', $nuevo === $total + 1);
} catch (Throwable $e) {
    check('Base de datos: ' . $e->getMessage(), false);
}

// 3. La web responde por DNS interno (APP_URL=http://app)
$url  = rtrim((string) getenv('APP_URL'), '/');
$html = @file_get_contents($url . '/');
check("GET $url/ responde", $html !== false);
check('La página muestra el paciente insertado', $html !== false && str_contains($html, '99999999-9'));

$health = @file_get_contents($url . '/health.php');
check('health.php devuelve ok', $health !== false && str_contains($health, '"ok"'));

echo PHP_EOL . ($fallos === 0 ? 'Todas las pruebas pasaron.' : "$fallos prueba(s) fallaron.") . PHP_EOL;
exit($fallos === 0 ? 0 : 1);
