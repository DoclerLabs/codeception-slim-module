help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test                    to perform tests."
	@echo "  coverage                to perform tests with code coverage."
	@echo "  static                  to run phpstan and php-cs-fixer check."
	@echo "  static-phpstan          to run phpstan."
	@echo "  static-cs-check         to run php-cs-fixer."
	@echo "  static-cs-fix           to run php-cs-fixer, writing the changes."

test:
	vendor/bin/codecept build
	vendor/bin/codecept run

coverage:
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage --coverage-xml --coverage-html

static: static-phpstan static-cs-check

static-phpstan:
	composer install
	composer bin phpstan install
	vendor/bin/phpstan analyze $(PHPSTAN_PARAMS)

static-cs-fix:
	docker run --rm -it -v ${PWD}:/app -w /app oskarstark/php-cs-fixer-ga:2.19.0 --diff-format udiff $(CS_PARAMS)

static-cs-check:
	$(MAKE) static-cs-fix CS_PARAMS="--dry-run"
