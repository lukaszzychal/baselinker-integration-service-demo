.PHONY: up down logs test phpstan cs-fix shell

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f app

test:
	docker compose exec app vendor/bin/phpunit

phpstan:
	docker compose exec app vendor/bin/phpstan analyse

cs-fix:
	docker compose exec app vendor/bin/php-cs-fixer fix

cs-check:
	docker compose exec app vendor/bin/php-cs-fixer fix --dry-run --diff

shell:
	docker compose exec app bash

install:
	docker compose exec app composer install

migrate:
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
