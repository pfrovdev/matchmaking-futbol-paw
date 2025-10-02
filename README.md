# TP Integrador Match Making Futbol 5

## Estructura del Proyecto

```bash
.
├── public/
│   ├── icons/
│   ├── css/
│   ├── js/
│   └── index.php
├── src/
│   ├── App/
│   │   ├── Controllers/
│   │   │   ├──AuthController.php
│   │   │   ├──EquipoController.php
│   │   │   ├──EstadoDesafioController.php
│   │   │   ├──EstadoIteracionController.php
│   │   │   ├──EstadoPartidoController.php
│   │   │   ├──PageController.php
│   │   │   └──TipoEquipoController.php
│   │   ├── Models/
│   │   │   ├──Comentario.php
│   │   │   ├──Desafio.php
│   │   │   ├──Equipo.php
│   │   │   ├──EquipoCollection.php
│   │   │   ├──Estadisticas.php
│   │   │   ├──EstadoDesafio.php
│   │   │   ├──EstadoIteracion.php
│   │   │   ├──EstadoPartido.php
│   │   │   ├──NivelElo.php
│   │   │   ├──Partido.php
│   │   │   ├──ResultadoPartido.php
│   │   │   └──TipoEquipo.php
│   │   ├── Utils/
│   │   │   └──CalculadoraDeElo.php
│   │   ├── Views/
│   │   │   └── parts/
│   │   │       ├──footer.php
│   │   │       ├──header-no-account.php
│   │   │       ├──header.php
│   │   │       ├──nav.php
│   │   │       ├──side-navbar.php
│   │   │       ├──tarjeta-desafio.php
│   │   │       ├──tarjeta-envio-desafio.php
│   │   │       └──tarjeta-historial.php
│   │   │   ├──about-us.php
│   │   │   ├──create-account.php
│   │   │   ├──create-team.php
│   │   │   ├──dashboard.php
│   │   │   ├──home.php
│   │   │   ├──internal-error.php
│   │   │   ├──login.php
│   │   │   ├──mail-desafio.html
│   │   │   ├──not-found.php
│   │   │   └──search-team.php
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
│   │   ├── JWT/
│   │   │   └── Auth.php
│   │   ├── Middelware/
│   │   │   └── AuthMiddelware.php
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
└── composer.json
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

## Instrucciones de uso (modo local)

### 1. Crear un directorio de trabajo
Elegir un directorio en tu máquina donde quieras alojar el proyecto y crealo desde la terminal:

```bash
mkdir ~/proyectos
cd ~/proyectos
```

### 2. Crear un directorio de trabajo
Dentro del directorio creado, clonar el repositorio:

```bash
git clone https://github.com/pfrovdev/matchmaking-futbol-paw.git
```

### 3. Ingresar al proyecto
Acceder al directorio del proyecto:

```bash
cd matchmaking-futbol-paw

```

### 4. Levantar los servicios
Ejecutar:

```bash
make up
```
Esto iniciará los servicios necesarios (PHP, MySQL, Redis) y la aplicación estará disponible ingreando en el navegador:

http://localhost:9999

### 5.Inicializar la base de datos
En otra terminal (sin detener los servicios anteriores), ejecutar:

```bash
make reset_db
```
Este comando:
    - Crea la base de datos.

    - Genera la estructura de tablas.

    - Aplica las migraciones necesarias.

### 6. Insertar datos de demostración
Para cargar datos ficticios en la base de datis y poder probar el sistema, correr:

```bash
make insertar_datos_demo
```

## Aplicación Web (versión online)

El sistema también está disponible en hosting:

http://34.30.100.40/


## Deploy en Google Cloud con Terraform y Kubernetes

Este proyecto fue desplegado en la plataforma gratuita de Google cloud utilizando una infraestructura orquestada con Terraform y Kubernetes.
A continuación, se documenta el procedimiento completo seguido para lograr el deploy de los servicios de la aplicación (Nginx, PHP/App y MySQL).

1. Construcción y Push de Imágenes Docker

Antes de desplegar en Kubernetes, fue necesario construir las imágenes Docker personalizadas para cada uno de los servicios:

Nginx → encargado de servir la aplicación y manejar el enrutamiento.

App/PHP → contiene el código de la aplicación web.

MySQL → base de datos persistente del sistema.

Cada imagen las construyó y se subió al Artifact Registry de Google cloud:

### Nginx
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/nginx:latest -f deploy/nginx/Dockerfile .
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/nginx:latest

### App/PHP
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/app:latest -f deploy/app/Dockerfile .
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/app:latest

### MySQL
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/mysql:latest -f deploy/mysql/Dockerfile .
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/mysql:latest

Con esto nos aseguramos que las imágenes estén disponibles en una ubicación centralizada y segura, optimizada para desplegar en Kubernetes dentro del mismo proyecto de Google cloud.

2. Manejo de configuraciones y secrets

### ConfigMaps

Las configuraciones de la aplicación (variables de entorno comunes como DB_HOST, DB_USER, JWT parámetros, correo, etc.) las gestionamos mediante un ConfigMap:

Archivo: deploy/kubernetes/configmap-env.yaml

Esto permite desacoplar las configuraciones del código y mantenerlas versionadas de forma clara.

### Secrets

Las credenciales sensibles (contraseñas de base de datos, JWT_SECRET, credenciales de email, etc.) se almacenaron como Secrets en Kubernetes:

kubectl create secret generic app-secrets \
  --from-literal=DB_ROOT_PASSWORD=blablabla \
  --from-literal=DB_PASSWORD=blablabla \
  --from-literal=JWT_SECRET=blablabla... \
  --from-literal=MAIL_PASSWORD=blablabla

Con esto, nos aseguramos que los valores confidenciales no estén en el código ni en el repositorio, cumpliendo buenas prácticas de seguridad.

3. Persistencia de la base de datos

La base de datos MySQL necesita almacenamiento persistente para no perder datos al reiniciar o actualizar los pods.
Se creó un PersistentVolumeClaim (PVC):

Archivo: deploy/kubernetes/mysql-pvc.yaml

resources:
  requests:
    storage: 5Gi

Esto reserva 5GB de almacenamiento en Google cloud, garantizando la durabilidad de los datos.

4. Despliegue de servicios en Kubernetes

Cada componente de la aplicación se define como un Deployment y expone sus puertos mediante un Service.

### Para MySQL

Archivo: deploy/kubernetes/mysql-deployment.yaml
Archivo: deploy/kubernetes/mysql-service.yaml

Deployment con 1 réplica.

Variables de entorno obtenidas de ConfigMap y Secrets.

Montaje del PVC en /var/lib/mysql.

Service expuesto en el puerto 3306.

### Para Nginx y App/PHP

Los deployments de Nginx y App siguen una estructura similar:

Imagen personalizada desde el Artifact Registry.

Variables de configuración mediante ConfigMaps y Secrets.

Exposición de puertos con Services.

Balanceo de carga manejado por Kubernetes (LoadBalancer).

5. Terraform para la Infraestructura

Para gestionar la infraestructura en Google cloud, utilizamos Terraform.
Entre los recursos definidos se encuentran:

Cluster de GKE (Google Kubernetes Engine)

Artifact Registry para almacenar imágenes Docker

Configuración de red y roles IAM necesarios

Terraform nos permite automatizar y versionar toda la infraestructura, garantizando que cualquier miembro del equipo pueda replicar el entorno con un simple terraform apply.

6. Flujo de deploy completo

Crear proyecto en Google cloud y habilitar la cuenta gratuita.

Configurar Terraform para aprovisionar el cluster de Kubernetes.

Construir y pushear las imágenes Docker al Artifact Registry.

Crear ConfigMaps y secrets en el cluster.

Aplicar los manifiestos de Kubernetes:

kubectl apply -f deploy/kubernetes/configmap-env.yaml
kubectl apply -f deploy/kubernetes/mysql-pvc.yaml
kubectl apply -f deploy/kubernetes/mysql-deployment.yaml
kubectl apply -f deploy/kubernetes/mysql-service.yaml
kubectl apply -f deploy/kubernetes/nginx-php-deployment.yaml
kubectl apply -f deploy/kubernetes/nginx-service.yaml
kubectl apply -f deploy/kubernetes/redis-deployment.yaml
kubectl apply -f deploy/kubernetes/redis-service.yaml

Verificar que los pods estén en estado running:
En una terminal ejecutar:
```bash
kubectl get pods
```

Acceder a la aplicación a través de la IP pública del LoadBalancer generado.
Para obtenerla ejecutar desde una terminal:
```bash
kubectl get svc
```

7. Resultados

La aplicación quedó desplegada en un cluster Kubernetes en Google cloud, completamente desacoplada y escalable.

Gracias a Terraform, la infraestructura es reproducible.

Los Secrets y ConfigMaps permiten un manejo seguro y ordenado de configuraciones.

La base de datos cuenta con almacenamiento persistente para garantizar integridad de los datos.