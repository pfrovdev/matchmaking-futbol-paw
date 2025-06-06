# TP Integrador. Match Making Futbol 5

## Autores:

De Paola Agustín, depa.agustin@gmail.com
Fedorov Pavlo, pfrov.dev@gmail.com
Iarza Esteban, iarzaesteban94@gmail.com
Juarez Sebastián, sebajuarezz@#gmail.com

## Estructura del Proyecto PawPrint

```bash
.
├── public/
│   ├── icons/
│   ├── css/
│   ├── js/
│   └── index.php
├── src/
│   ├── App/
│   │   ├── Controller/
│   │   ├── Models/
│   │   ├── Views/
│   │   │   └── Parts/
│   ├── Config/
│   │   └── config.php
│   ├── Core/
│   │   ├── Database/
│   │   │   ├── ConnectionBuilder.php
│   │   │   ├── Database.php
│   │   │   └── QueryBuilder.php
│   │   ├── Exceptions/
│   │   │   ├── InvalidValueFormatException.php
│   │   │   └── RouteNotFoundException.php
│   │   ├── Traits/
│   │   │   └── Loggeable.php
│   │   ├── AbstractController.php
│   │   ├── AbstractModel.php
│   │   ├── ModelFactory.php
│   │   ├── Request.php
│   │   └── Router.php
│   ├── Deploy_database/
│   │   ├── README.md
│   │   └── database_schema.sql
│   └── bootstrap.php
├── vendor/
├── .env
├── Makefile
├── phinx.php
├── composer.json
└── README.md
```

## Análisis de peticiones HTTP

Responsable: index.php + Router (Core/Router.php)

Descripción: El archivo public/index.php actúa como Front Controller. Toma la URL solicitada por el navegador ($\_SERVER['REQUEST_URI']) y la pasa al enrutador (Router) para determinar qué controlador debe manejarla.

## Mapeo de URLs en funcionalidades

Responsable: Router (Core/Router.php)

Descripción: Mapea rutas como /books o /about-us con métodos de controladores (PageController@books).

## Generación de respuestas HTTP

Responsable: Controladores (App/Controllers/_.php) + Vistas (App/Views/_.php)

Descripción: Cada controlador se encarga de procesar la lógica de la solicitud y retornar una vista (HTML, PDF, etc.). Además, puede establecer códigos HTTP como http_response_code(404).

## Generación de registros

Responsable: bootstrap.php + Monolog

Descripción: Se utiliza Monolog para registrar errores, info de rutas, excepciones no capturadas, etc. Ideal para debug en desarrollo.

## Persistencia

Responsable: Modelos (App/Models/\*.php) y un posible Database en Core/

Descripción: Por ahora, no se implementa, pero se contempla creando un espacio para modelos y conexión a base de datos futura

## Configuración

Responsable: Archivo src/Config/config.php

Descripción: La configuración central se debe almacenar en un lugar único. Ahí van las rutas de logs, entorno (DEBUG, PRODUCTION), rutas a recursos, etc.

## Diferentes representaciones de la información

Responsable: Controladores + Vistas + Librerías externas

Descripción:

- HTML → Views/\*.php
- JSON → echo json_encode($data);

## Tecnologías y Herramientas

- PHP >= 7.4.3
- Composer para gestión de dependencias
- Sistema de logs ubicado en `/Logs/logs.app`

## Instrucciones de uso

1. Clonar el repositorio

```bash
git clone https://github.com/pfrovdev/matchmaking-futbol-paw.git
```

2. Levantar el proyecto localmente

```bash
cd ~/matchmaking-futbol-paw
make up
```

Esto iniciará un servidor PHP local en `http://localhost:9999`.

## Recursos del proyecto
