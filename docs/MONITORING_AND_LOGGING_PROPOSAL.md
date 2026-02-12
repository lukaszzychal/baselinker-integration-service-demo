# Monitorowanie i logowanie błędów oraz wydajności modułu

Dokument opisuje **propozycję** oraz **zaimplementowane** mechanizmy monitorowania i logowania błędów oraz wydajności modułu integracji z API Baselinkera (wymaganie: *„Zaproponuj i zaimplementuj sposób monitorowania i logowania błędów oraz wydajności modułu”*).

---

## 1. Wymaganie

> Zaproponuj i zaimplementuj sposób monitorowania i logowania błędów oraz wydajności modułu.

---

## 2. Propozycja (przyjęte podejście)

Przyjęto następujące założenia:

| Obszar | Propozycja |
|--------|------------|
| **Wydajność** | Każde wywołanie API Baselinkera jest mierzone (czas wykonania w ms) i logowane w ustrukturyzowanym formacie (JSON), z możliwością agregacji w zewnętrznych systemach. |
| **Błędy** | Błędy wywołań API są rejestrowane w tym samym strumieniu metryk (status `failure` + komunikat błędu); błędy aplikacji trafiają do głównego kanału logów. |
| **Miejsce zapisu** | W środowiskach **staging** i **produkcja** wszystkie logi (w tym metryki) kierowane są na **wyjście standardowe (stderr)** w formacie JSON, aby mogły być przechwycone przez orchestrator (Docker, K8s) lub agenta (Datadog, Grafana Loki, Promtail) bez zapisu na dysk. |
| **Integracja z zewnętrznymi narzędziami** | Aplikacja nie zależy od konkretnego systemu monitoringu; wystarczy przechwycenie stdout/stderr i parsowanie JSON. Opcjonalnie można dodać eksport do Prometheus, Datadog, Grafana.
---

## 3. Zaimplementowane elementy

### 3.1. Metryki wydajności i statusu (success/failure)

**Komponent:** `App\Integration\Decorator\MetricsClientDecorator`

- **Cel:** Pomiar czasu wykonania i rejestracja sukcesu lub błędu dla **każdego** wywołania API Baselinkera.
- **Dane logowane (kanał `metrics`):**
  - `method` — nazwa metody (getOrders, getOrderSources, getOrderStatusList, getOrderTransactionData)
  - `duration_ms` — czas wywołania w milisekundach
  - `status` — `success` lub `failure`
  - `error` — przy `status === 'failure'`: komunikat wyjątku
- **Format:** Jedna linia JSON na zdarzenie (w staging/prod dzięki konfiguracji Monolog).
- **Pozycja w łańcuchu:** Dekorator owijający klienta API; nie zmienia sygnatur, tylko mierzy i loguje.


---

### 3.2. Logowanie operacji (audit / ślad działania)

**Komponent:** `App\Integration\Decorator\LoggingClientDecorator`

- **Cel:** Logowanie **operacji** wykonywanych na API (np. „pobieranie zamówień”, „pobrano N zamówień”) — poziom `info`.
- **Zastosowanie:** Śledzenie przepływu, audyt, diagnostyka (np. jakie filtry były użyte, ile zamówień zwrócono).
- **Nie zastępuje** metryk: metryki = czas + success/failure; logging = opis działania.

---

### 3.3. Obsługa błędów API

- **Wyjątki:** `BaselinkerApiException`, `InvalidResponseException`, `RateLimitExceededException` — używane przy błędach API lub nieprawidłowej odpowiedzi.
- **Metryki:** W `MetricsClientDecorator` przy każdym złapanym wyjątku logowane jest zdarzenie z `status: 'failure'` i `error: $e->getMessage()`, więc błędy API są **widoczne w strumieniu metryk** bez osobnego handlera.
- **Aplikacja:** Błędy w kontrolerach/komendach (np. „Order not found”, błędy walidacji) mogą być logowane przez standardowy logger aplikacji; w prod stosowany jest handler `fingers_crossed`, który zapisuje bufor logów dopiero przy wystąpieniu błędu.

---

### 3.4. Konfiguracja logów (Monolog)

**Plik:** `config/packages/monolog.yaml`

| Środowisko | Kanał / handler | Cel |
|------------|------------------|-----|
| **dev** | `main` → plik `%kernel.logs_dir%/%kernel.environment%.log` | Logi aplikacji na dysk. |
| **dev** | `metrics` → plik `%kernel.logs_dir%/metrics.log` | Metryki API w osobnym pliku. |
| **staging** | `metrics`, `main`, `deprecation` → **php://stderr**, format **JSON** | Wszystkie logi na wyjście, do przechwycenia przez zewnętrzny system. |
| **prod** | `metrics` → **php://stderr**, JSON | Metryki na stderr. |
| **prod** | `main` → fingers_crossed → `nested` → **php://stderr**, JSON | Logi aplikacji (w tym przy błędach) na stderr. |
| **prod** | `deprecation` → **php://stderr**, JSON | Deprecacje na stderr. |

**Znaczenie `path: "php://stderr"` (np. linia 45 w monolog.yaml):**

- `php://stderr` to w PHP **strumień standardowego wyjścia błędów** (stderr).
- W konfiguracji Monolog `path: "php://stderr"` oznacza, że handler **zapisuje każdy log bezpośrednio na stderr**, zamiast do pliku.
- Dzięki temu w kontenerze / procesie logi nie trafiają na dysk — wychodzą strumieniem, który może przechwycić Docker, Kubernetes, systemd lub agent (Datadog, Promtail, Fluentd) i przekazać do systemu logów lub metryk (Grafana Loki, Datadog, Elasticsearch itd.).

W **staging** (linia 45) dotyczy to handlera **metrics**: w tym środowisku wszystkie wpisy z kanału `metrics` są wysyłane na stderr w formacie JSON.

---

## 4. Podsumowanie: co jest logowane i gdzie

| Co | Gdzie (komponent) | Kanał / wyjście | Środowisko |
|----|--------------------|------------------|-------------|
| Czas + success/failure każdego wywołania API | MetricsClientDecorator | `metrics` → stderr (JSON) lub plik metrics.log | staging/prod → stderr; dev → plik |
| Opis operacji (np. „fetching orders”, „count”) | LoggingClientDecorator | logger aplikacji (main) | wszystkie |
| Błędy wywołań API | MetricsClientDecorator (catch) | `metrics` z status=failure, error=… | wszystkie |
| Błędy i logi aplikacji | Symfony / Monolog | main → stderr (JSON) lub plik | staging/prod → stderr; dev → plik |
| Deprecacje PHP/Symfony | Monolog | deprecation → stderr (JSON) lub domyślne | staging/prod → stderr |

---
