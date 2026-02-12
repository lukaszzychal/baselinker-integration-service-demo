.PHONY: up down logs test test-setup phpstan cs-fix shell

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f app

# Run tests. First run (or after Dockerfile/composer change): make test-setup then make test; or just make test (builds and prepares DB).
test: test-setup
	docker compose exec app composer test

# Build app image (APCu for Ganesha), start containers, create test DB via Doctrine, install deps, migrate.
test-setup:
	docker compose build app
	docker compose up -d
	@echo "Waiting for DB..."
	@for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15; do \
		docker compose exec -T db pg_isready -U app -d baselinker_dev 2>/dev/null && break; \
		echo "  attempt $$i/15..."; sleep 1; \
	done
	@docker compose exec -T -u root app sh -c 'mkdir -p /app/var/cache /app/var/log && chown -R www-data:www-data /app/var' 2>/dev/null || true
	docker compose exec -T app composer install --no-interaction --prefer-dist
	docker compose exec -T app php bin/console doctrine:database:drop --env=test --force --if-exists
	docker compose exec -T app php bin/console doctrine:database:create --env=test
	docker compose exec -T app php bin/console doctrine:migrations:migrate --no-interaction --env=test

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
