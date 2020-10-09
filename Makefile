help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test                    to perform tests."
	@echo "  coverage                to perform tests with code coverage."
	@echo "  static-phpstan          to run phpstan on the codebase."
	@echo "  static-cs-fix           to run php-cs-fixer, writing the changes."
	@echo "  static-cs-check         to run php-cs-fixer."

.PHONY: test
test:
	vendor/bin/codecept build
	vendor/bin/codecept run

.PHONY: coverage
coverage:
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage --coverage-xml --coverage-html

.PHONY: static
static: static-phpstan static-cs-check

static-phpstan:
	docker run --rm -it -e REQUIRE_DEV=true -v ${PWD}:/app -w /app oskarstark/phpstan-ga:0.12.41 analyze $(PHPSTAN_PARAMS)

static-cs-fix:
	docker run --rm -it -v ${PWD}:/app -w /app oskarstark/php-cs-fixer-ga:2.16.4 --diff-format udiff $(CS_PARAMS)

static-cs-check:
	$(MAKE) static-cs-fix CS_PARAMS="--dry-run"
