help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test       to perform tests."
	@echo "  coverage   to perform tests with code coverage."

.PHONY: test
test:
	vendor/bin/codecept build
	vendor/bin/codecept run

.PHONY: coverage
coverage:
	vendor/bin/codecept build
	vendor/bin/codecept run --coverage --coverage-xml --coverage-html
