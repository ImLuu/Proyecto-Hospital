# Entorno de desarrollo (Dev)

Levanta la web (PHP 8.2 + Apache + Bootstrap 5) y PostgreSQL en tu PC.
Lo que edites en `frontend/` se ve al instante en el navegador (hot-reload).

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y abierto.

## Primer arranque

1. Crea tu archivo de variables a partir de la plantilla:

   ```bash
   cp .env.example .env
   ```

   (En Windows sin terminal Bash: copia `.env.example` y renombra la copia a `.env`.)
   Cambia `DB_PASSWORD` por una clave propia.

2. Levanta los contenedores:

   ```bash
   docker compose -f docker-compose.dev.yml up -d --build
   ```

3. Abre <http://localhost:8080>. Deberías ver la lista de pacientes.

## Qué se levanta

| Servicio | Qué es | Desde tu PC | Desde otro contenedor |
|---|---|---|---|
| `app` | PHP 8.2 + Apache | <http://localhost:8080> | `http://app` |
| `db`  | PostgreSQL 16 | `localhost:5432` | `db:5432` |

**DNS interno:** dentro de Docker cada servicio se llama por su nombre. Por eso
en `.env` pones `DB_HOST=db` y no una IP.

## Comandos útiles

| Acción | Comando |
|---|---|
| Ver logs | `docker compose -f docker-compose.dev.yml logs -f` |
| Parar | `docker compose -f docker-compose.dev.yml down` |
| Parar y **borrar la BD** | `docker compose -f docker-compose.dev.yml down -v` |
| Consola SQL | `docker compose -f docker-compose.dev.yml exec db psql -U hospital_user -d hospital` |
| Reconstruir tras cambiar el `Dockerfile` | `docker compose -f docker-compose.dev.yml up -d --build` |

## Conectarte a la BD con DBeaver / pgAdmin

Host `localhost`, puerto `5432` (o `DB_EXTERNAL_PORT`), y usuario, clave y base
de tu `.env`.

## Cambiar la estructura de la BD

Los archivos de `db/init/` **solo se ejecutan la primera vez** que se crea la BD.
Si modificas `01_schema.sql`, recrea la base (se pierden los datos):

```bash
docker compose -f docker-compose.dev.yml down -v
```

```bash
docker compose -f docker-compose.dev.yml up -d
```

## Problemas frecuentes

- **"port is already allocated"**: el 8080 o el 5432 ya están en uso. Cambia
  `APP_PORT` o `DB_EXTERNAL_PORT` en `.env`.
- **"No se pudo conectar con la base de datos"**: revisa que `.env` existe y que
  `DB_HOST=db`. Mira los logs con `docker compose -f docker-compose.dev.yml logs db`.
