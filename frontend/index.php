<?php
require __DIR__ . '/config/db.php';

$error   = null;
$mensaje = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rut      = trim($_POST['rut'] ?? '');
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $fechaNac = ($_POST['fecha_nac'] ?? '') ?: null;

        if ($rut === '' || $nombre === '' || $apellido === '') {
            $error = 'RUT, nombre y apellido son obligatorios.';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO pacientes (rut, nombre, apellido, fecha_nac) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$rut, $nombre, $apellido, $fechaNac]);
            $mensaje = 'Paciente registrado correctamente.';
        }
    }

    $pacientes = db()->query('SELECT * FROM pacientes ORDER BY id DESC')->fetchAll();
} catch (PDOException $e) {
    $pacientes = [];
    $error = $e->getCode() === '23505'
        ? 'Ya existe un paciente con ese RUT.'
        : 'No se pudo conectar con la base de datos.';
}

function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proyecto Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand">🏥 Proyecto Hospital</span>
    </div>
</nav>

<main class="container">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= e($mensaje) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">Nuevo paciente</div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label" for="rut">RUT</label>
                            <input class="form-control" id="rut" name="rut" placeholder="12345678-9" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="nombre">Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="apellido">Apellido</label>
                            <input class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="fecha_nac">Fecha de nacimiento</label>
                            <input class="form-control" id="fecha_nac" name="fecha_nac" type="date">
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Guardar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">Pacientes (<?= count($pacientes) ?>)</div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr><th>#</th><th>RUT</th><th>Nombre</th><th>Fecha nac.</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pacientes as $p): ?>
                            <tr>
                                <td><?= e((string) $p['id']) ?></td>
                                <td><?= e($p['rut']) ?></td>
                                <td><?= e($p['nombre'] . ' ' . $p['apellido']) ?></td>
                                <td><?= e($p['fecha_nac']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$pacientes): ?>
                            <tr><td colspan="4" class="text-center text-muted">Sin pacientes</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
