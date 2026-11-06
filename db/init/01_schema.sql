-- Se ejecuta automáticamente la primera vez que arranca PostgreSQL en Docker.
-- En el hosting final, ejecútalo a mano desde phpPgAdmin o psql.
CREATE TABLE IF NOT EXISTS pacientes (
    id         SERIAL PRIMARY KEY,
    rut        VARCHAR(12)  NOT NULL UNIQUE,
    nombre     VARCHAR(100) NOT NULL,
    apellido   VARCHAR(100) NOT NULL,
    fecha_nac  DATE,
    creado_en  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO pacientes (rut, nombre, apellido, fecha_nac) VALUES
    ('11111111-1', 'Ana',  'Pérez', '1985-04-12'),
    ('22222222-2', 'Luis', 'Soto',  '1990-09-30')
ON CONFLICT (rut) DO NOTHING;
