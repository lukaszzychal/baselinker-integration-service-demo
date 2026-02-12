<?php

namespace App\Repository;

use App\Entity\Order;

interface OrderRepositoryInterface
{
    public function findByExternalId(string $externalId, string $marketplace): ?Order;
    public function save(Order $order, bool $flush = false): void;
}
