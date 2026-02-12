# Testy manualne (QA) – baselinker-integration-service-demo

Instrukcje krok po kroku z gotowymi komendami do wklejenia w terminal (curl, CLI). Baza URL API: `http://localhost:8000` (Symfony) – w Dockerze użyj adresu kontenera zamiast localhost.

---

## Wymagania wstępne

1. **Środowisko**
   - PHP 8.4+ z rozszerzeniami: apcu, json, mbstring, openssl, pdo, xml, zip, intl (w Dockerze APCu jest już w obrazie).
   - Skopiuj `.env` do `.env.local` i ustaw `BASELINKER_API_TOKEN` (prawidłowy token do testów „happy path”, nieprawidłowy do testów Circuit Breaker).

2. **Uruchomienie aplikacji**
   ```bash
   cd baselinker-integration-service-demo
   composer install
   php bin/console doctrine:migrations:migrate --no-interaction
   symfony server:start -d
   ```
   **Lub w Dockerze:** najpierw zbuduj obraz, potem uruchom kontenery:
   ```bash
   docker compose build app
   docker compose up -d
   make migrate   # wymagane przed użyciem /api/orders (tworzy tabelę order)
   ```
   API z hosta: **http://localhost:8080** (nginx). Wewnątrz sieci Docker: `http://app` lub `http://nginx`.  
   **Jeśli GET /api/orders zwraca 500 (relation "order" does not exist)** – uruchom migracje: `make migrate` lub `docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction`.

3. **Baza URL**  
   W przykładach poniżej: `http://localhost:8000` (Symfony CLI) lub **`http://localhost:8080`** (Docker). Zamień na swój URL, jeśli inny.

4. **Strona powitalna i dokumentacja API**
   - **GET /** – strona powitalna z linkami do dokumentacji i health.
   - **GET /api/doc** – Swagger UI (interaktywna dokumentacja OpenAPI).
   - **GET /api/doc.json** – specyfikacja OpenAPI w JSON.

---

## 1. Health check

**Cel:** Sprawdzenie, że aplikacja odpowiada.

**Request (wklej w terminal):**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" http://localhost:8000/api/health
```

**Oczekiwana odpowiedź:** Status 200, np.:
```json
{"status":"ok"}
```
oraz w ostatniej linii: `HTTP_CODE:200`.

---

## 2. Pobieranie zamówień przez API (POST)

**Cel:** Wywołanie joba pobierania zamówień z Baselinker.

**Request (wklej w terminal):**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" -X POST http://localhost:8000/api/orders/fetch \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Opcjonalny body z filtrami (np. data od, źródło):**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" -X POST http://localhost:8000/api/orders/fetch \
  -H "Content-Type: application/json" \
  -d '{"date_from": 1704067200, "filter_order_source": "allegro"}'
```
(`date_from` = Unix timestamp, np. 1704067200 = 2024-01-01 00:00:00 UTC)

**Oczekiwana odpowiedź:** Status **202**, np.:
```json
{"status":"Order fetch job dispatched"}
```
oraz `HTTP_CODE:202`.

---

## 3. Lista zamówień (GET)

**Cel:** Sprawdzenie, że endpoint listy zamówień odpowiada (dane zależą od stanu bazy).

**Request (wklej w terminal):**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" "http://localhost:8000/api/orders"
```

**Oczekiwana odpowiedź:** Status 200, body to tablica zamówień (np. `[]` lub lista obiektów) oraz `HTTP_CODE:200`.

---

## 4. Pobieranie zamówień przez CLI

**Cel:** Wysłanie joba pobierania zamówień z konsoli.

**Krok 1 – domyślna data (ostatni dzień), wszystkie źródła:**
```bash
php bin/console app:fetch-orders
```

**Oczekiwany output:**  
`Orders fetch command dispatched successfully!`

**Krok 2 – z opcją marketplace i datą:**
```bash
php bin/console app:fetch-orders --marketplace=allegro --from=2024-01-01
```

**Oczekiwany output:**  
`Orders fetch command dispatched successfully!`

---

## 5. Circuit Breaker – odpowiedź 503 (otwarty obwód)

**Cel:** Sprawdzenie, że po przekroczeniu progu błędów API Baselinker aplikacja zwraca **503** z JSON `error: service_unavailable`.

**Uwaga:** Jeśli w pętli dostajesz **HTTP 000** – curl w ogóle nie łączy się z serwerem (aplikacja nie działa pod danym adresem lub zły port). Sprawdź: `curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/api/health` (Docker) lub `http://localhost:8000/api/health` (Symfony CLI) – powinno być **200**. Upewnij się, że serwis jest włączony (`make up` / `symfony server:start`) i że w pętli używasz tego samego URL co w wymaganiach (np. **8080** dla Docker).

**Warunek:** Ganesha używa strategii Rate: w oknie 30 s, przy **min. 10 requestach** i **≥50% błędów** obwód się otwiera. Wiadomość `FetchOrders` jest obsługiwana **synchronicznie** (transport `sync`), więc każdy `POST /api/orders/fetch` od razu wywołuje Baselinker w tej samej sesji – błędy są liczone i po otwarciu obwodu ten sam endpoint zwraca 503.

**Krok 1 – ustaw nieprawidłowy token (żeby każde wywołanie Baselinker kończyło się błędem)**  
W pliku `.env.local`:
```bash
BASELINKER_API_TOKEN=invalid_token_for_testing
```
Zrestartuj aplikację, żeby wczytała nowy env:
- **Symfony CLI:** `symfony server:stop` → `symfony server:start -d`
- **Docker:** `docker compose restart app` (w katalogu projektu; albo `docker restart baselinker_app`)

**Krok 2 – wykonaj min. 10 requestów (wszystkie zakończą się błędem po stronie Baselinker)**  
Wklej w terminal (np. 12 wywołań):
```bash
for i in {1..12}; do
  curl -s -o /dev/null -w "%{http_code}\n" -X POST http://localhost:8000/api/orders/fetch \
    -H "Content-Type: application/json" \
    -d '{}'
done
```
Początkowo możesz zobaczyć 500 lub 401 (błąd z Baselinker). Po przekroczeniu progu Ganesha **otworzy obwód** i kolejne wywołania dostaną **503**.

**Krok 3 – sprawdź odpowiedź 503**  
Jedno wywołanie z wypisaniem body i statusu:
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" -X POST http://localhost:8000/api/orders/fetch \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Oczekiwana odpowiedź przy otwartym obwodzie:** Status **503**, body np.:
```json
{"error":"service_unavailable","message":"Baselinker API is temporarily unavailable. Please try again later."}
```
oraz w ostatniej linii: `HTTP_CODE:503`.

**Weryfikacja:** W odpowiedzi musi być `"error":"service_unavailable"` i status HTTP 503.

**Po teście:** Przywróć prawidłowy `BASELINKER_API_TOKEN` w `.env.local` i zrestartuj aplikację (jak wyżej: Symfony CLI lub Docker).

---

## 6. Circuit Breaker – powrót do normalnego działania (half-open)

**Cel:** Po czasie **intervalToHalfOpen** (domyślnie 10 s) Ganesha przechodzi w stan half-open i przepuszcza jedną próbę; sukces zamyka obwód.

**Kroki:**
1. Otwórz obwód (sekcja 5: nieprawidłowy token, min. 10× POST `/api/orders/fetch`).
2. Przywróć **prawidłowy** token w `.env.local` i zrestartuj aplikację (Symfony CLI lub `docker compose restart app`).
3. Odczekaj **ok. 10 sekund** (w tym czasie obwód przejdzie w half-open).
4. Wykonaj **jeden** request:
   ```bash
   curl -s -w "\nHTTP_CODE:%{http_code}\n" -X POST http://localhost:8000/api/orders/fetch \
     -H "Content-Type: application/json" \
     -d '{}'
   ```

**Oczekiwane zachowanie:** Pierwsza próba jest przepuszczana; przy poprawnym tokenie Baselinker odpowie OK i obwód się zamyka – odpowiedź **202** i `{"status":"Order fetch job dispatched"}`. Kolejne requesty działają normalnie (202).

---

## 7. Inne endpointy (szybka weryfikacja)

**Lista statusów zamówień:**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" http://localhost:8000/api/order-statuses
```

**Lista źródeł zamówień:**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" http://localhost:8000/api/order-sources
```

**Szczegóły zamówienia (podstaw ID):**
```bash
curl -s -w "\nHTTP_CODE:%{http_code}\n" "http://localhost:8000/api/orders/1"
```

Oczekiwane: status 200 (lub 404, jeśli brak zasobu) i odpowiedni JSON.

---

## Podsumowanie

| Scenariusz              | Metoda / narzędzie | Oczekiwany status |
|-------------------------|--------------------|--------------------|
| Health check            | GET /api/health    | 200                |
| Fetch orders (API)      | POST /api/orders/fetch | 202           |
| Lista zamówień          | GET /api/orders    | 200                |
| Fetch orders (CLI)      | app:fetch-orders   | exit 0             |
| Circuit Breaker (open)  | POST /api/orders/fetch (gdy obwód otwarty) | 503 |
| Half-open recovery      | Po ~10 s, jeden request | 202 lub 200   |

W razie problemów sprawdź logi: `var/log/dev.log` (lub kanał `metrics`) oraz konfigurację `BASELINKER_API_TOKEN` i dostępność APCu (`php -m | grep apcu`).
