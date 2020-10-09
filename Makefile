help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test             to perform tests."
	@echo "  coverage         to perform tests with code coverage."
	@echo "  static-phpstan   to run phpstan on the codebase"

.PHONY: test
test:
	vendor/bin/codecept build
	vendor/bin/codecept run

.PHONY: coverage
coverage:
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage --coverage-xml --coverage-html

.PHONY: static-phpstan
static-phpstan:
	docker run --rm -it -e REQUIRE_DEV=true -v ${PWD}:/app -w /app oskarstark/phpstan-ga:0.12.41 analyze $(PHPSTAN_PARAMS)
