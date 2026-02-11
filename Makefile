.PHONY: up down logs test phpstan cs-fix shell

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f app

test:
	docker compose exec app composer test

test-contract:
	docker compose exec app composer test:contract

phpstan:
	docker compose exec app composer analyse

cs-fix:
	docker compose exec app composer cs:fix

cs-check:
	docker compose exec app composer cs:check

shell:
	docker compose exec app bash

install:
	docker compose exec app composer install

migrate:
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
