<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class OrderDTO
{
    public function __construct(
        public string $externalId,
        public string $marketplace,
        public string $customerName,
        public float $totalAmount,
        public \DateTimeImmutable $createdAt,
        /** @var array<int, array<string, mixed>> */
        public array $products = [],
    ) {
    }
}
