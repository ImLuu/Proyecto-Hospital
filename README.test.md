# Entorno de testing (Test)

Entorno **efímero**: se crea, ejecuta las pruebas y se destruye.
La base de datos vive en RAM (`tmpfs`), así que cada ejecución empieza limpia.
No necesita `.env` y no abre puertos en tu PC.

## Ejecutar las pruebas

```bash
docker compose -f docker-compose.test.yml up --build --abort-on-container-exit --exit-code-from tests
```

Al terminar, limpia los contenedores:

```bash
docker compose -f docker-compose.test.yml down
```

Resultado esperado:

```
tests-1  | [OK]   PHP es 8.2
tests-1  | [OK]   Extensión pdo_pgsql cargada
tests-1  | [OK]   Conexión PDO a db
tests-1  | [OK]   Tabla pacientes con datos iniciales
tests-1  | [OK]   Insertar paciente
tests-1  | [OK]   GET http://app/ responde
tests-1  | [OK]   La página muestra el paciente insertado
tests-1  | [OK]   health.php devuelve ok
tests-1  |
tests-1  | Todas las pruebas pasaron.
```

El comando devuelve código `0` si todo pasa y `1` si algo falla, así que sirve
también en CI (por ejemplo, GitHub Actions).

## Cómo funciona

| Servicio | Función |
|---|---|
| `db` | PostgreSQL 16 en RAM, con el esquema de `db/init/` |
| `app` | La web con el código **copiado dentro de la imagen** (como en producción, sin volumen) |
| `tests` | Ejecuta `tests/smoke_test.php` y termina |

Los servicios esperan a estar sanos antes de arrancar (`healthcheck`):
`db` → `app` → `tests`.

## Diferencias con Dev

| | Dev | Test |
|---|---|---|
| Datos | Persisten (volumen `pgdata`) | En RAM, se borran |
| Código | Montado (hot-reload) | Copiado en la imagen |
| Puertos abiertos | 8080 y 5432 | Ninguno |
| Variables | `.env` | Fijas en `docker-compose.test.yml` |
| Nombre del proyecto | `hospital-dev` | `hospital-test` (pueden convivir) |

## Añadir pruebas

Agrega más llamadas a `check('descripción', condición);` en `tests/smoke_test.php`.
