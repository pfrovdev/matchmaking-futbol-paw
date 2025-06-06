# MATCHMAKING deploy

## Para el redeploy

## Luego de realizar los cambios que necesitamos en el código:

## Regenerar la imagen de docker desde la raiz del repo con:

docker build -t gcr.io/matchmaking-app-paw/php-app:<NOMBRE-TAG> -f deploy/app/Dockerfile .

## Pushear la nueva imagen de docker con:

docker push gcr.io/matchmaking-app-paw/php-app:<NOMBRE-TAG>

## Luego en el deployment deploy/kubernetes/nginx-php-deployment.yaml, modificar el tag de la imagen:

cambiar esto:

image: gcr.io/matchmaking-app-paw/php-app:<NOMBRE-ANTERIOR-TAG>

por esto:

image: gcr.io/matchmaking-app-paw/php-app:<NOMBRE-NUEVO-TAG>

## Luego aplicamos el nuevo deployment desde la raiz del repo con:

kubectl apply -f deploy/kubernetes/nginx-php-deployment.yaml

## Podemos ver los pods running con:

kubectl get pods

## Como obtener los servicios y la ip púplica para el acceso (Buscar nginx con EXTERNAL-IP):

kubectl get svc
