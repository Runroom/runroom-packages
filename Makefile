UID = $(shell id -u)
GID = $(shell id -g)
PHP_VERSION="8.3"
CONTAINER_NAME = runroom_packages
DOCKER_RUN = docker run --rm --volume $(PWD):/usr/app -w /usr/app $(CONTAINER_NAME)

up:
	docker build -t $(CONTAINER_NAME) .
	docker run -d -v $(PWD):/usr/app --name $(CONTAINER_NAME) $(CONTAINER_NAME)
.PHONY: up

halt:
	@if [ `docker ps --filter name=$(CONTAINER_NAME) --format "{{.ID}}"` ]; then \
        docker stop $(CONTAINER_NAME); \
    fi
.PHONY: halt

destroy:
	@if [ `docker ps --filter name=$(CONTAINER_NAME) --format "{{.ID}}"` ]; then \
        docker rm -f $(CONTAINER_NAME); \
    fi
.PHONY: destroy

build: halt destroy
	docker build --build-arg UID=$(UID) --build-arg GID=$(GID) -t $(CONTAINER_NAME) .
.PHONY: build

provision: composer-install

down:
	docker stop $(CONTAINER_NAME) && docker rm $(CONTAINER_NAME)
.PHONY: down

ssh:
	docker run --rm -it -v $(PWD):/usr/app -w /usr/app $(CONTAINER_NAME) sh
.PHONY: ssh

composer-update:
	$(DOCKER_RUN) composer update --optimize-autoloader
.PHONY: composer-update

composer-install:
	$(DOCKER_RUN) composer install --optimize-autoloader
.PHONY: composer-install

composer-normalize:
	$(DOCKER_RUN) composer normalize
.PHONY: composer-normalize

phpstan:
	$(DOCKER_RUN) composer phpstan
.PHONY: phpstan

psalm:
	$(DOCKER_RUN) composer psalm -- --php-version=$(PHP_VERSION)
.PHONY: psalm

php-cs-fixer:
	$(DOCKER_RUN) composer php-cs-fixer
.PHONY: php-cs-fixer

phpunit:
	$(DOCKER_RUN) phpunit
.PHONY: phpunit

phpunit-coverage:
	$(DOCKER_RUN) phpunit --coverage-html /usr/app/coverage
.PHONY: phpunit-coverage

rector:
	$(DOCKER_RUN) composer rector
.PHONY: rector

lint-qa:
	$(DOCKER_RUN) composer php-cs-fixer
	$(DOCKER_RUN) phpunit
	$(DOCKER_RUN) composer phpstan
	$(DOCKER_RUN) composer psalm -- --php-version=$(PHP_VERSION)
	$(DOCKER_RUN) composer rector
	$(DOCKER_RUN) composer normalize
	$(DOCKER_RUN) bin/console lint:container
	$(DOCKER_RUN) bin/console lint:twig src
	$(DOCKER_RUN) bin/console lint:xliff src
	$(DOCKER_RUN) bin/console lint:yaml src
.PHONY: lint-qa
