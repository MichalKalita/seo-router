.PHONY: tests install # do NOT skip tests if folder exists

all: install tests

install:
	composer self-update
	composer update --no-interaction

tests:
	./vendor/bin/tester ./tests/
