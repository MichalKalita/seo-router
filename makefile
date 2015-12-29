.PHONY: tests prepare # do NOT skip tests if folder exists

all: prepare tests

prepare:
	composer self-update
	composer update --no-interaction

tests:
	./vendor/bin/tester ./tests/
