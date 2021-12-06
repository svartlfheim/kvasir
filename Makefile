PROJECT_NAME=kvasir
DOCKER_COMPOSE=docker compose -f ./docker-compose.yml --project-name="$(PROJECT_NAME)_dev"

ifndef REGISTRY
	REGISTRY=kvasir.local
endif

ifndef TAG
	TAG=local
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
	docker build -t ${REGISTRY}/nginx:${TAG} ./docker/nginx

.PHONY: build-php-fpm
build-php-fpm: ## Builds the php-fpm images (including cron and dev variants)
	docker build --target php-fpm -t ${REGISTRY}/php-fpm:${TAG} ./docker/php-fpm
	docker build --target cron -t ${REGISTRY}/php-fpm:cron-${TAG} ./docker/php-fpm
	docker build --target dev -t ${REGISTRY}/php-fpm/dev:${TAG} ./docker/php-fpm
	docker build --target cron-dev -t ${REGISTRY}/php-fpm/dev:cron-${TAG} ./docker/php-fpm

.PHONY: local-build
local-build: ## Builds all images required by the local docker-compose environment
	$(DOCKER_COMPOSE) build --no-cache

.PHONY: up
up: ## Starts up the local docker-compose environments
	$(DOCKER_COMPOSE) up -d

.PHONY: exec
exec: ## Open a shell in the php service (for running console commands and such)
	$(DOCKER_COMPOSE) exec phpfpm /bin/bash

.PHONY: down
down: ## Stops the local docker-compose environment
	$(DOCKER_COMPOSE) down

.PHONY: reset
reset: down local-build up ## Destroys the environment, rebuilds all images, and starts up again