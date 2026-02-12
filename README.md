# Baselinker Integration Service

Serwis integracyjny do komunikacji z [API Baselinker](https://api.baselinker.com/). Umożliwia pobieranie zamówień i ich przetwarzanie w ramach aplikacji Symfony (CLI, API HTTP, Messenger, Circuit Breaker z Ganesha + APCu).

## Wymagania

- Docker i Docker Compose  
- (opcjonalnie) Composer i PHP 8.4+ z rozszerzeniami: apcu, pdo_pgsql, intl, zip, mbstring, xml, opcache – do uruchomienia bez Docker

## Szybki start (Docker)

```bash
# Uruchom kontenery (app, nginx, PostgreSQL)
make up

# Zastosuj migracje (baza dev)
make migrate

# Testy (build obrazu, utworzenie bazy testowej, migracje, PHPUnit)
make test
```

Aplikacja: **http://localhost:8080** (nginx). Health check: `curl http://localhost:8080/api/health`.

## Makefile – najważniejsze cele

| Cel | Opis |
|-----|------|
| `make up` | Uruchamia kontenery w tle (`docker compose up -d`). |
| `make down` | Zatrzymuje i usuwa kontenery. |
| `make test` | Uruchamia **test-setup** (jeśli potrzeba), potem **PHPUnit** (unit + integration). |
| `make test-setup` | Buduje obraz aplikacji (z APCu dla Circuit Breaker), włącza kontenery, czeka na PostgreSQL, instaluje zależności (`composer install`), **tworzy bazę testową przez Doctrine** (`doctrine:database:drop` + `doctrine:database:create --env=test`) i wykonuje migracje w env test. Baza testowa nie wymaga ręcznego tworzenia w PostgreSQL – Doctrine tworzy ją na podstawie `DATABASE_URL` z `.env.test`. |
| `make test-contract` | Testy kontraktu z prawdziwym API Baselinker (wymaga `BASELINKER_API_TOKEN`, grupa `external`). |
| `make phpstan` | Analiza statyczna (PHPStan). |
| `make cs-fix` | Poprawki stylu kodu (PHP-CS-Fixer). |
| `make cs-check` | Sprawdzenie stylu bez zmian. |
| `make shell` | Wejście do kontenera aplikacji (`bash`). |
| `make install` | `composer install` w kontenerze. |
| `make migrate` | Migracje Doctrine w env **dev**. |
| `make logs` | Logi kontenera aplikacji (`docker compose logs -f app`). |

## Testy

- **`make test`** – uruchamia cały zestaw testów (unit + integration). Przed testami **test-setup**:
  - buduje obraz (m.in. APCu),
  - wznosi kontenery i czeka na bazę,
  - instaluje zależności,
  - **tworzy bazę testową automatycznie** (`doctrine:database:drop --env=test --force --if-exists` oraz `doctrine:database:create --env=test`),
  - wykonuje migracje w env test.
- Testy integracyjne używają bazy z env `test` (Doctrine `dbname_suffix` w konfiguracji testowej).
- Testy manualne (QA): scenariusze krok po kroku i gotowe komendy `curl`/CLI są w **[docs/QA_MANUAL_TESTING.md](docs/QA_MANUAL_TESTING.md)**.

## Konfiguracja

- Skopiuj `.env.local.dist` do `.env.local` (lub uzupełnij `.env`) i ustaw `BASELINKER_API_TOKEN` oraz ewentualnie `DATABASE_URL` (w Dockerze domyślnie: `postgresql://app:secret@db:5432/baselinker_dev`).
- Baza testowa: w `.env.test` ustawiona jest `DATABASE_URL` na bazę testową; Doctrine w env test dodaje `dbname_suffix`, więc faktyczna baza to m.in. `baselinker_test_test`. Tworzenie i usuwanie tej bazy odbywa się przez `doctrine:database:create` / `doctrine:database:drop` w **test-setup**.

## Ref

- getOrders: https://api.baselinker.com/index.php?method=getOrders
