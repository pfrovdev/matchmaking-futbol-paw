# MATCHMAKING deploy

## Conexion:
gcloud auth login

gcloud container clusters get-credentials matchmaking-cluster --region us-central1

## Para el redeploy

## Luego de realizar los cambios que necesitamos en el código:

## Regenerar la imagen de docker desde la raiz del repo con:
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/app:latest -f deploy/app/Dockerfile .
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/nginx:latest -f deploy/nginx/Dockerfile .
docker build -t us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/mysql:latest -f deploy/mysql/Dockerfile .
## Pushear la nueva imagen de docker con:
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/app:latest
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/nginx:latest
docker push us-central1-docker.pkg.dev/matchmaking-paw/matchmaking-repo/mysql:latest
## Luego en el deployment deploy/kubernetes/nginx-php-deployment.yaml, modificar el tag de la imagen:


## Luego aplicamos el nuevo deployment desde la raiz del repo con:

kubectl apply -f deploy/kubernetes/nginx-php-deployment.yaml

## Podemos ver los pods running con:

kubectl get pods

## Como obtener los servicios y la ip púplica para el acceso (Buscar nginx con EXTERNAL-IP):

kubectl get svc


## Aplicar todos los deployments:

kubectl apply -f deploy/kubernetes/configmap-env.yaml
kubectl apply -f deploy/kubernetes/mysql-pvc.yaml
kubectl apply -f deploy/kubernetes/mysql-deployment.yaml
kubectl apply -f deploy/kubernetes/mysql-service.yaml
kubectl apply -f deploy/kubernetes/nginx-php-deployment.yaml
kubectl apply -f deploy/kubernetes/nginx-service.yaml
kubectl apply -f deploy/kubernetes/redis-deployment.yaml
kubectl apply -f deploy/kubernetes/redis-service.yaml

## Ver los sercicios corriendo:

kubectl get pods

## Ver logs 

kubectl logs deployment/mysql
kubectl logs deployment/web -c php
kubectl logs deployment/web -c nginx

## Restart de servicios
Para el servicio web
kubectl rollout restart deployment web

Para la base de datos
kubectl rollout restart deployment mysql

Para redis
kubectl rollout restart deployment redis


## Ingresar a un contenedor:
kubectl exec -it -n <POD-NAME> -c web -- /bin/sh

Una vez dentro podemos ejecutar el script para cargar la base de datos

php src/Deploy_database/insert_demo_data.php


para rdeploy mysql:
docker build -t gcr.io/matchmaking-app-paw/mysql:v1 -f deploy/mysql/Dockerfile .
docker push gcr.io/matchmaking-app-paw/mysql:v1



## conectar al pod:
kubectl get pods -l app=web
kubectl exec -it NOMBRE_DEL_POD -c php -- bash
php src/Deploy_database/insert_demo_data.php


kubectl get pods -l app=mysql
kubectl exec -it NOMBRE_DEL_POD -- bash
