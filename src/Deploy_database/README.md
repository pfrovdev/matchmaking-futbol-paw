# Estructura de la base de datos

# Comandos para ejecutar con mysql

## Para listar las bases de datos

```bash
SHOW DATABASES;
```

## Usar una base de datos específica

```bash
USE nombre_base_de_datos;
```

Ejemplo:
USE match_making_db;

## Listar todas las tablas de la base de datos activa

```bash
SHOW TABLES;
```

## Ver la estructura (schema) de una tabla: campos, tipos, nulos, claves primarias/foráneas, etc.

```bash
SHOW COLUMNS FROM nombre_tabla;
```

o

```bash
DESCRIBE nombre_tabla;
```

Ejemplo:
DESCRIBE usuario;
o
SHOW COLUMNS FROM usuario;

## Ver el SQL completo usado para crear una tabla (ideal para ver índices, claves foráneas, etc.)

```bash
SHOW CREATE TABLE nombre_tabla\G
```

Ejemplo:
SHOW CREATE TABLE equipo\G

El \G al final hace que se muestre de forma vertical, le da mas legibilidad
