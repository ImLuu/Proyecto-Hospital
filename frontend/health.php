<?php
// Comprobación de salud: responde 200 si PHP y la base de datos funcionan.
require __DIR__ . '/config/db.php';

header('Content-Type: application/json');
try {
    db()->query('SELECT 1');
    echo json_encode(['status' => 'ok', 'php' => PHP_VERSION]);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['status' => 'error']);
}
