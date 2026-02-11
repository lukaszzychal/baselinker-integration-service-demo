<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\OrderDTO;
use App\Integration\BaselinkerClientInterface;
use App\Integration\OrderMapper;

final readonly class OrderFetchService
{
    public function __construct(
        private BaselinkerClientInterface $client,
        private OrderMapper $mapper,
    ) {
    }

    /** @return array<int, OrderDTO> */
    public function fetchOrders(\DateTimeInterface $from): array
    {
        $result = $this->client->getOrders($from);

        /** @var array<int, array<string, mixed>> $orders */
        $orders = $result['orders'] ?? [];

        return array_map(
            fn (array $raw) => $this->mapper->map($raw),
            $orders
        );
    }
}
