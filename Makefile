UID = $(shell id -u)
GID = $(shell id -g)
PHP_VERSION="8.1"
CONTAINER_NAME = runroom_packages
docker-exec = docker run --rm -v $(PWD):/usr/app -w /usr/app $(CONTAINER_NAME) $(1)

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
	$(call docker-exec,composer update --optimize-autoloader)
.PHONY: composer-update

composer-install:
	$(call docker-exec,composer install --optimize-autoloader)
.PHONY: composer-install

composer-normalize:
	$(call docker-exec,composer normalize)
.PHONY: composer-normalize

phpstan:
	$(call docker-exec,composer phpstan)
.PHONY: phpstan

psalm:
	$(call docker-exec,composer psalm -- --php-version=$(PHP_VERSION))
.PHONY: psalm

php-cs-fixer:
	$(call docker-exec,composer php-cs-fixer)
.PHONY: php-cs-fixer

phpunit:
	$(call docker-exec,phpunit)
.PHONY: phpunit

phpunit-coverage:
	$(call docker-exec,phpunit --coverage-html /usr/app/coverage)
.PHONY: phpunit-coverage

rector:
	$(call docker-exec,composer rector)
.PHONY: rector

lint-qa:
	$(call docker-exec,composer php-cs-fixer)
	$(call docker-exec,phpunit --coverage-html /usr/app/coverage)
	$(call docker-exec,composer phpstan)
	$(call docker-exec,composer psalm -- --php-version=$(PHP_VERSION))
	$(call docker-exec,composer rector)
	$(call docker-exec,composer normalize)
	$(call docker-exec,bin/console lint:container)
	$(call docker-exec,bin/console lint:twig src)
	$(call docker-exec,bin/console lint:xliff src)
	$(call docker-exec,bin/console lint:yaml src)
.PHONY: lint-qa
