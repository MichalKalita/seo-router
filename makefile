.PHONY: tests install api # do NOT skip tests if folder exists

all: install api tests

install:
	composer self-update
	composer update --no-interaction

api:
	rm -rf api
	vendor/bin/apigen generate -s src -d api

tests:
	vendor/bin/tester tests/
