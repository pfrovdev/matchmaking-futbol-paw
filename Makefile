include .env

export

# Para levantar la App
up:
	composer update
	docker-compose up -d db
	composer start

# Para actualizar dependencias compose
update:
	composer update

# Para crear base de datos
create_database:
	@echo "Esperando a que MySQL esté listo..."
	@echo "Creando la base de datos..."
	docker-compose exec db mysql -u $(DB_USER) -p$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS $(DB_NAME);"
	@echo "Aplicando el esquema de la base de datos..."
	docker-compose exec db bash -c "mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) < /docker-entrypoint-initdb.d/database_schema.sql"
	@echo "Base de datos $(DB_NAME) creada con éxito!"

# Borra la base de datos
drop_database:
	@echo "¿Estás seguro de que deseas borrar la base de datos? (s/n)"
	@read confirm && if [ $$confirm = "s" ]; then \
		docker-compose exec db mysql -u $(DB_USER) -p$(DB_PASSWORD) -e "DROP DATABASE IF EXISTS $(DB_NAME);"; \
		echo "Base de datos $(DB_NAME) eliminada."; \
	else \
		echo "Operación cancelada."; \
	fi

# Resetea la base de datos de cero y arma nueva estructura si hay cambios en database_schema.sql
reset_db: down_db
	docker-compose down -v
	docker-compose up -d db
	@sleep 10
	make drop_database
	make create_database

# Ingresar a la shell de la base de datos
db_shell:
	docker exec -it match_making_db mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)

# Baja el contenedor de la base de datos
down_db:
	docker-compose down

# Resetea contenedor de la base de datos
restart_db:
	docker-compose down
	docker-compose up -d db

# Ver logs del contenedor de la base de datos
logs_db:
	docker-compose logs -f db

# Realiza dump de la base de datos y lo vuelca en /tmp
save_dump:
	@echo "Generando dump de la base de datos..."
	docker exec match_making_db mysqldump -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) > /tmp/match_making_db.sql
	@echo "Dump guardado en /tmp/match_making_db.sql."

# Realiza un restpre de la base de datos y lo levanta de /tmp/match_making_db.sql
restore_db:
	@echo "Restaurando base de datos desde /tmp/match_making_db.sql..."
	docker exec -i match_making_db mysql -u $(DB_USER) -p$(DB_PASSWORD) $(DB_NAME) < /tmp/match_making_db.sql
	@echo "Base de datos restaurada desde /tmp/match_making_db.sql."