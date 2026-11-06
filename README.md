# Proyecto Hospital

Aplicación web en **PHP 8.2 + Apache + Bootstrap 5** que usa **PostgreSQL**
mediante **PDO**.

## Estructura

| Archivo / carpeta | Para qué sirve |
|---|---|
| `docker-compose.dev.yml` | Desarrollo con hot-reload y puertos abiertos |
| `docker-compose.test.yml` | Testing efímero con BD en RAM |
| `.env.example` | Plantilla de variables para crear `.env` |
| `frontend/` | Código PHP y HTML con Bootstrap 5 (lo que se sube al hosting) |
| `Dockerfile` | Imagen PHP 8.2 + Apache + `pdo_pgsql` (Dev y Test) |
| `db/init/` | SQL que crea las tablas |
| `tests/` | Pruebas que ejecuta el entorno de testing |
| `README.dev.md` | Guía del entorno de desarrollo |
| `README.test.md` | Guía del entorno de testing |
| `README.hosting.md` | Guía de despliegue final |

## Inicio rápido

```bash
cp .env.example .env
```

```bash
docker compose -f docker-compose.dev.yml up -d --build
```

Abre <http://localhost:8080>.

## Arquitectura

```
          Tu navegador
               │ :8080
┌──────────────▼───────────── red interna de Docker ─┐
│  app (PHP 8.2 + Apache) ──PDO──►  db (PostgreSQL)  │
│        DB_HOST=db  (DNS interno por nombre)        │
└────────────────────────────────────────────────────┘
```

Toda la configuración viene de variables de entorno (`DB_HOST`, `DB_NAME`,
`DB_USER`, `DB_PASSWORD`...), así el mismo código funciona en Dev, en Test y en
el hosting.
