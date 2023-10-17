help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  tests                   to perform tests."
	@echo "  coverage                to perform tests with code coverage."
	@echo "  static                  to run phpstan and php-cs-fixer check."
	@echo "  static-phpstan          to run phpstan."
	@echo "  static-psalm            to run psalm."
	@echo "  static-cs-check         to run php-cs-fixer."
	@echo "  static-cs-fix           to run php-cs-fixer, writing the changes."

tests:
	vendor/bin/codecept build
	vendor/bin/codecept run

coverage:
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage --coverage-xml --coverage-html

static: static-phpstan static-psalm static-cs-check

static-phpstan:
	composer install
	composer bin phpstan install
	vendor/bin/phpstan analyze $(PHPSTAN_PARAMS)

static-psalm:
	composer install
	composer bin psalm install
	vendor/bin/psalm.phar $(PSALM_PARAMS)

static-cs-fix:
	composer install
	composer bin php-cs-fixer install
	vendor/bin/php-cs-fixer fix --diff $(CS_PARAMS)

static-cs-check:
	$(MAKE) static-cs-fix CS_PARAMS="--dry-run"

DOCKER_RUN=docker run --rm -u $(shell id -u):$(shell id -g) -v $(shell pwd):/app -w /app

local-ci:
	$(DOCKER_RUN) -v ~/.composer:/tmp -v ~/.ssh:/root/.ssh composer:2 install
	$(DOCKER_RUN) php:7.2-cli vendor/bin/codecept build
	$(DOCKER_RUN) php:7.2-cli vendor/bin/codecept run
	$(DOCKER_RUN) php:7.3-cli vendor/bin/codecept run
	$(DOCKER_RUN) php:7.4-cli vendor/bin/codecept run
	$(DOCKER_RUN) php:8.0-cli vendor/bin/codecept run
	$(DOCKER_RUN) php:8.1-cli vendor/bin/codecept run
	$(DOCKER_RUN) php:8.2-cli vendor/bin/codecept run
