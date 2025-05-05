.SHELLFLAGS := -euc
DOCKER_DIR := docker

PHP_INI_EXAMPLES := $(DOCKER_DIR)/php/examples/ini
PHP_INI_FILES := $(DOCKER_DIR)/php/assets/ini

DOCKER_ENV_FILE := $(DOCKER_DIR)/.env
DOCKER_ENV_EXAMPLE := $(DOCKER_DIR)/.env.example

ifeq (,$(wildcard $(DOCKER_ENV_FILE)))
  include $(DOCKER_ENV_EXAMPLE)
  export $(shell sed 's/=.*//' $(DOCKER_ENV_EXAMPLE))
  $(shell cp -f $(DOCKER_ENV_EXAMPLE) $(DOCKER_ENV_FILE))
endif

ifneq (,$(wildcard $(DOCKER_ENV_FILE)))
  include $(DOCKER_ENV_FILE)
  export $(shell sed 's/=.*//' $(DOCKER_ENV_FILE))
endif

ifeq ($(shell uname), Darwin)
	SED_INPLACE_FLAG=-i ''
	FORCED_REWRITE_FLAG=-f
	XDEBUG_CLIENT_HOST := host.docker.internal
else
	SED_INPLACE_FLAG=-i
	FORCED_REWRITE_FLAG=--remove-destination
	XDEBUG_CLIENT_HOST := $(shell hostname -I | cut -d" " -f1)
endif

install: \
	install-docker-build \
	install-php-ini-files \
	install-database \
	install-app-env \
	install-composer-packages \
	install-migrations \
	up \
	start-workers

install-docker-build:
	@echo "Building docker images"
	cd ./docker && \
	cp -f compose.override.yml.example compose.override.yml && \
	sed $(SED_INPLACE_FLAG) "s/HOST_UID:.*/HOST_UID: $(shell id -u)/" compose.override.yml && \
	docker compose build

install-php-ini-files:
	@echo "Preparing php ini files" && \
	rm -rf $(PHP_INI_FILES) && mkdir -p $(PHP_INI_FILES) && \
	cp $(PHP_INI_EXAMPLES)/*.ini.example $(PHP_INI_FILES)/ && \
	for f in $(PHP_INI_FILES)/*.ini.example; do \
	  mv "$$f" "$${f%.example}"; \
	done && \
	sed $(SED_INPLACE_FLAG) "s/XDEBUG_CLIENT_HOST/${XDEBUG_CLIENT_HOST}/" $(PHP_INI_FILES)/xdebug.ini

install-database:
	@echo "Preparing database"
	cd ./docker && \
	docker compose up -d mariadb && \
	docker run --rm --network $(COMPOSE_PROJECT_NAME)_network jwilder/dockerize -wait tcp://$(COMPOSE_PROJECT_NAME)_mariadb:3306 -timeout 30s

install-app-env:
	@echo "Preparing app env file"
	cp -f ./app/.env.example ./app/.env

install-composer-packages:
	@echo "Installing composer packages"
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli sh -c "composer install"

install-migrations:
	@echo "Installing migrations"
	cd ./docker && \
	docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli bin/console doctrine:migrations:migrate --no-interaction && \
	docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli bin/console --env=test doctrine:migrations:migrate --no-interaction

start-workers:
	@if [ -n "$$(docker ps -q --filter 'name=$(COMPOSE_PROJECT_NAME)-async-workers' --filter 'status=running')" ]; then \
		echo "Attaching to running workers..."; \
		docker attach $(COMPOSE_PROJECT_NAME)-async-workers; \
	else \
		echo "Starting async workers..."; \
		cd ./docker && \
		docker compose run --rm \
			-u www-data \
			-e XDEBUG_MODE=off \
			--name=$(COMPOSE_PROJECT_NAME)-async-workers \
			php-cli \
			bin/console messenger:consume async_command -vv; \
	fi

stop-workers:
	@echo "Stopping Symfony Messenger async workers..."
	cd ./docker && \
	docker exec -it -u www-data -e XDEBUG_MODE=off \
	$(COMPOSE_PROJECT_NAME)-async-workers \
	bin/console messenger:stop-workers

up:
	cd ./docker && docker compose up -d
	@echo "Application is available at: http://127.0.0.1:8080/api/doc"

clean:
	cd ./docker && docker compose down -v
	git clean -fdx -e .idea

test:
	cd ./docker && docker compose run --rm -u www-data -it -e XDEBUG_MODE=off php-cli sh -c "composer qa"

db-log:
	cd ./docker && docker compose exec mariadb tail -f /var/log/mysql/query.log

sh:
	cd ./docker && docker compose run --rm -u www-data -it php-cli sh -l

run:
	docker exec -it -e XDEBUG_MODE=off $(COMPOSE_PROJECT_NAME)_php bin/console app:parse-log ./data/logs.log 10
