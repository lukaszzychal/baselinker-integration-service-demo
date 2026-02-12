<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class OrderStatusDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $nameForCustomer,
        public string $color,
    ) {
    }
}
