# Mapowanie Pól: Baselinker API -> Nasza Aplikacja

Dokumentacja mapowania danych pobieranych z metody `getOrders` Baselinkera do wewnętrznego obiektu `OrderDTO`.

| Pole Baselinker (Raw API) | Pole w `OrderDTO` (Aplikacja) | Typ w DTO | Opis / Transformacja |
| :--- | :--- | :--- | :--- |
| `order_id` | `externalId` | `string` | Unikalny ID zamówienia w Baselinker. Konwersja int -> string. |
| `order_source` | `marketplace` | `string` | Źródło zamówienia (np. `amazon`, `allegro`, `ebay`). |
| `user_login` | `customerName` | `string` | Login lub nazwa kupującego. |
| `date_add` | `createdAt` | `\DateTimeImmutable` | Konwersja z formatu Timestamp (Unix) na obiekt daty. |
| `products` | `products` | `array` | Surowa lista produktów (do dalszej obróbki w przyszłości). |
| *Brak (Wartość obliczona)* | `totalAmount` | `float` | Suma: `(products.price_brutto * products.quantity) + delivery_price`. |


