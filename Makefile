include .env
export

# Detectar SO y definir pausa portable
ifeq ($(OS),Windows_NT)
	SLEEP := timeout /t 10 /nobreak > NUL
else
	SLEEP := sleep 10
endif

# Levantar la App
up:
	composer update
	docker-compose up -d db
	composer start

# Actualizar dependencias# Actualizar dependencias\update:
	composer update

# Crear base de datos
create_database:
	@echo "Esperando a que MySQL esté listo..."
	@$(SLEEP)
	@echo "Creando la base de datos..."
	docker-compose exec db mysql -u $(DB_USER) -p$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS $(DB_NAME);"
	@echo "Aplicando el esquema de la base de datos..."
	docker-compose exec db bash -c "mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) < /docker-entrypoint-initdb.d/database_schema.sql"
	@echo "Base de datos $(DB_NAME) creada con éxito!"

# Borrar base de datos con confirmación interactiva
drop_database:
ifeq ($(OS),Windows_NT)
	@powershell -Command "$$c = Read-Host 'Estas seguro de que deseas borrar la base de datos? (s/n)'; if ($$c -eq 's') { docker-compose exec db mysql -u $(DB_USER) -p$(DB_PASSWORD) -e 'DROP DATABASE IF EXISTS $(DB_NAME);'; Write-Output 'Base de datos $(DB_NAME) eliminada.' } else { Write-Output 'Operacion cancelada.' }"
else
	@bash -lc "read -p '¿Estás seguro de que deseas borrar la base de datos? (s/n) ' c; [[ $$c == 's' ]] && docker-compose exec db mysql -u $(DB_USER) -p$(DB_PASSWORD) -e \"DROP DATABASE IF EXISTS $(DB_NAME);\" && echo 'Base de datos $(DB_NAME) eliminada.' || echo 'Operación cancelada.'"
endif

# Resetear la base de datos de cero y recrear la estructura
reset_db: down_db
	docker-compose down -v
	docker-compose up -d db
	@$(SLEEP)
	make drop_database
	make create_database

# Insertar datos demo
insertar_datos_demo:
	php src/Deploy_database/insert_demo_data.php

# Ingresar a la shell de la base de datos
db_shell:
	docker exec -it match_making_db mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)

# Bajar contenedores
down_db:
	docker-compose down

# Reiniciar contenedor DB
restart_db:
	docker-compose down
	docker-compose up -d db

# Ver logs del contenedor DB
logs_db:
	docker-compose logs -f db

# Dump de la base de datos
save_dump:
	@echo "Generando dump de la base de datos..."
	docker exec match_making_db mysqldump -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) > /tmp/match_making_db.sql
	@echo "Dump guardado en /tmp/match_making_db.sql."

# Restaurar base de datos desde dump
restore_db:
	@echo "Restaurando base de datos desde /tmp/match_making_db.sql..."
	docker exec -i match_making_db mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) < /tmp/match_making_db.sql
	@echo "Base de datos restaurada desde /tmp/match_making_db.sql."