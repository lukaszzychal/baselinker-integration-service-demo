<?php

declare(strict_types=1);

namespace App\Integration;

use App\DTO\OrderDTO;

class OrderMapper
{
    /**
     * @param array<string, mixed> $rawOrder
     */
    public function map(array $rawOrder): OrderDTO
    {
        return new OrderDTO(
            externalId: (string) ($rawOrder['order_id'] ?? ''),
            marketplace: (string) ($rawOrder['order_source'] ?? 'unknown'),
            customerName: (string) ($rawOrder['user_login'] ?? ''),
            totalAmount: $this->calculateTotal($rawOrder),
            createdAt: new \DateTimeImmutable('@' . ($rawOrder['date_add'] ?? time())),
            products: (array) ($rawOrder['products'] ?? []),
        );
    }

    /**
     * @param array<string, mixed> $order
     */
    private function calculateTotal(array $order): int
    {
        $total = 0;
        /** @var array<int, array<string, mixed>> $products */
        $products = (array) ($order['products'] ?? []);

        foreach ($products as $product) {
            $price = (float) ($product['price_brutto'] ?? 0.0);
            $qty = (int) ($product['quantity'] ?? 1);
            $total += (int) round($price * 100) * $qty;
        }

        $deliveryPrice = (float) ($order['delivery_price'] ?? 0.0);

        return $total + (int) round($deliveryPrice * 100);
    }
}
