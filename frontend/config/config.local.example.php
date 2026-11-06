<?php
// SOLO PARA HOSTING TRADICIONAL (sin variables de entorno).
// Copia este archivo como config.local.php en el servidor y pon los datos
// que te da tu proveedor. config.local.php NO se sube a Git (.gitignore).
return [
    'DB_HOST'     => 'localhost',
    'DB_PORT'     => '5432',
    'DB_NAME'     => 'usuario_hospital',
    'DB_USER'     => 'usuario_hospital',
    'DB_PASSWORD' => 'clave_del_hosting',
];
