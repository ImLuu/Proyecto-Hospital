# Despliegue final (hosting tradicional)

En producción **no se usa Docker**: se sube la carpeta `frontend/` a un hosting
compartido con PHP y PostgreSQL (cPanel, Plesk, etc.).

## Requisitos del hosting

- PHP **8.2** o superior, con la extensión **`pdo_pgsql`** activada
  (en cPanel: *Select PHP Version → Extensions*).
- Una base de datos **PostgreSQL**. Muchos hostings solo ofrecen MySQL:
  confírmalo antes de contratar.
- Apache con soporte para `.htaccess` (lo normal).

## Pasos

### 1. Crear la base de datos

En el panel del hosting (*PostgreSQL Databases*):

1. Crea la base y el usuario, y asigna el usuario a la base.
2. Anota host, puerto, base, usuario y clave.
3. Abre **phpPgAdmin** y ejecuta el contenido de `db/init/01_schema.sql`.

### 2. Subir los archivos

Sube **el contenido** de `frontend/` (no la carpeta en sí) a `public_html/`
por el Administrador de archivos o por FTP (FileZilla):

```
public_html/
├── index.php
├── health.php
└── config/
    ├── .htaccess        ← bloquea el acceso web a esta carpeta
    ├── db.php
    └── config.local.php ← lo creas tú en el paso 3
```

### 3. Configurar la conexión

Los hostings compartidos no suelen permitir variables de entorno, así que
`config/db.php` lee un archivo alternativo:

1. En el servidor, copia `config/config.local.example.php` como
   `config/config.local.php`.
2. Pon en él los datos del paso 1. El host suele ser `localhost`, no `db`:
   el DNS interno `db` solo existe dentro de Docker.

`config.local.php` está en `.gitignore`: **nunca subas la contraseña a GitHub**.

### 4. Comprobar

- `https://tu-dominio/health.php` debe responder `{"status":"ok",...}`.
- `https://tu-dominio/config/db.php` debe dar **403 Forbidden**.
- `https://tu-dominio/` muestra la aplicación.

## Configuración por entorno

| Variable | Dev (Docker) | Test (Docker) | Hosting |
|---|---|---|---|
| Fuente | `.env` | `docker-compose.test.yml` | `config/config.local.php` |
| `DB_HOST` | `db` | `db` | `localhost` (o el que indique el hosting) |

El código PHP es el mismo en los tres: solo cambia de dónde salen los datos.

## Actualizar una versión

1. Ejecuta las pruebas: `docker compose -f docker-compose.test.yml up --build --abort-on-container-exit --exit-code-from tests`.
2. Sube los archivos modificados de `frontend/`, **sin pisar** `config/config.local.php`.
3. Si cambió la base de datos, aplica el SQL nuevo en phpPgAdmin.
