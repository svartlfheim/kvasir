PROJECT_NAME=kvasir
MINICA_IMAGE_NAME=kvasir_minica:latest
DOCKER_IMAGES_DIR=./docker/images
DOCKER_COMPOSE=docker compose -f ./docker-compose.yml --project-name="$(PROJECT_NAME)_dev"

ifndef REGISTRY
	REGISTRY=kvasir.local
endif

ifndef TAG
	TAG=local
endif

# This is a generice argument allowing us to pass args to tasks
# This won't always be used
ifndef ARGS
	ARGS=""
endif

# This is a combination of the following suggestions:
# https://gist.github.com/prwhite/8168133#gistcomment-1420062
help: ## This help dialog.
	@IFS=$$'\n' ; \
	help_lines=(`fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##/:/'`); \
	printf "%-30s %s\n" "target" "help" ; \
	printf "%-30s %s\n" "------" "----" ; \
	for help_line in $${help_lines[@]}; do \
			IFS=$$':' ; \
			help_split=($$help_line) ; \
			help_command=`echo $${help_split[0]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
			help_info=`echo $${help_split[2]} | sed -e 's/^ *//' -e 's/ *$$//'` ; \
			printf '\033[36m'; \
			printf "%-30s %s" $$help_command ; \
			printf '\033[0m'; \
			printf "%s\n" $$help_info; \
	done

.PHONY: build-nginx
build-nginx: ## Builds the NGiNX image
	docker build -t ${REGISTRY}/nginx:${TAG} ${DOCKER_IMAGES_DIR}/nginx

.PHONY: build-php-fpm
build-php-fpm: ## Builds the php-fpm images (including cron and dev variants)
	docker build --target php-fpm -t ${REGISTRY}/php-fpm:${TAG} ${DOCKER_IMAGES_DIR}/php-fpm
	docker build --target cron -t ${REGISTRY}/php-fpm:cron-${TAG} ${DOCKER_IMAGES_DIR}/php-fpm
	docker build --target dev -t ${REGISTRY}/php-fpm/dev:${TAG} ${DOCKER_IMAGES_DIR}/php-fpm
	docker build --target cron-dev -t ${REGISTRY}/php-fpm/dev:cron-${TAG} ${DOCKER_IMAGES_DIR}/php-fpm

.PHONY: local-build
local-build: ## Builds all images required by the local docker-compose environment
	$(DOCKER_COMPOSE) build

.PHONY: dockerup
dockerup:
	$(DOCKER_COMPOSE) up -d

.PHONY: up
up: dockerup api-migrate ## Starts up the local docker-compose environments

.PHONY: api-exec
api-exec: ## Open a shell in the php service (for running console commands and such)
	$(DOCKER_COMPOSE) exec phpfpm /bin/bash

.PHONY: api-migrate
api-migrate:
	$(DOCKER_COMPOSE) exec phpfpm bin/console doctrine:migrations:migrate --no-interaction

.PHONY: down
down: ## Stops the local docker-compose environment
	$(DOCKER_COMPOSE) down

.PHONY: api-utest
api-utest: ## Run unit tests for API
	$(DOCKER_COMPOSE) exec phpfpm ./bin/phpunit --testsuite unit $(ARGS)

.PHONY: api-itest
api-itest: ## Run integration tests for API
	$(DOCKER_COMPOSE) exec phpfpm ./bin/phpunit --testsuite integration $(ARGS)

.PHONY: api-test
api-test: api-utest api-itest ## Run all tests for API

.PHONY: api-lint
api-lint: ## Run cs fixer on api codebase
	$(DOCKER_COMPOSE) exec phpfpm ./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --allow-risky yes

.PHONY: lint
lint: api-lint ## Run all linters for the repo

.PHONY: api-stan
api-stan: ## Run the phpstan analysis on src
	$(DOCKER_COMPOSE) exec phpfpm vendor/bin/phpstan analyse -c phpstan.neon

.PHONY: api-csfix
api-csfix: ## Run cs fixer on api codebase
	$(DOCKER_COMPOSE) exec phpfpm ./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --allow-risky yes

.PHONY: postgres-psql
postgres-psql: ## Start a pgsql session inside the postgres container
	$(DOCKER_COMPOSE) exec postgres /bin/bash -c "psql -U\$$POSTGRES_USER  \$$POSTGRES_DB"

.PHONY: postgres-bash
postgres-exec: ## Start a bash shell session inside the postgres container
	$(DOCKER_COMPOSE) exec postgres bin/bash

.PHONY: mysql-shell
mysql-shell: ## Start a mysql session inside the mysql container
	$(DOCKER_COMPOSE) exec mysql /bin/bash -c "mysql -u\$$MYSQL_USER -p\$$MYSQL_PASSWORD \$$MYSQL_DATABASE"

.PHONY: mysql-bash
mysql-exec: ## Start a bash shell session inside the mysql container
	$(DOCKER_COMPOSE) exec mysql bin/bash

.PHONY: restart
restart: down up ## Destroys the environment and then starts it

.PHONY: reset
reset: down local-build up ## Destroys the environment, rebuilds all images, and starts up again

.PHONY: init
init: cp-env gen-certs tls-trust-ca rm-minica-image ## Initialises the environment, run this when you first clone the repo

.PHONY: build-minica-image
build-minica-image: ## Builds the minica docker image, used for generating tls certs locally
	docker build -t ${MINICA_IMAGE_NAME} ${DOCKER_IMAGES_DIR}/minica

.PHONY: rm-minica-image
rm-minica-image: ## Removes the minica docker image
	docker rmi ${MINICA_IMAGE_NAME}

.PHONY: gen-certs
gen-certs: build-minica-image ## Generates the certificates used locally
	git clean -fxd certs/*
	docker run -v "$(shell pwd)/certs:/srv" -w /srv --rm ${MINICA_IMAGE_NAME} minica --domains kvasir.local

.PHONY: cp-env
cp-env: ## Copy the .env.example to .env ready for use
	if [ ! -f ./.env ]; then cp ./.env.example ./.env; fi;

.PHONY: tls-trust-ca
tls-trust-ca: ## Trust the self-signed HTTPS certification
	sudo security add-trusted-cert -d -r trustRoot -k "/Library/Keychains/System.keychain" "./certs/minica.pem"

.PHONY: setup-hooks
setup-hooks: ## Sets up the default git hooks stored in ./hooks
	git config core.hooksPath $(shell pwd)/hooks