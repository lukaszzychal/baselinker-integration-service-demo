<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;

interface OrderRepositoryInterface
{
    /** @return list<Order> */
    public function findAll(): array;

    public function findById(int $id): ?Order;

    public function findByExternalId(string $externalId, string $marketplace): ?Order;

    public function save(Order $order, bool $flush = false): void;
}
