<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;

class OrderRepositorySpy implements OrderRepositoryInterface
{
    public bool $saveWasCalled = false;
    public ?Order $lastSavedOrder = null;
    /** @var array<string, Order> */
    private array $orders = [];

    public function findByExternalId(string $externalId, string $marketplace): ?Order
    {
        // Simulate database lookup
        return $this->orders[$externalId . '-' . $marketplace] ?? null;
    }

    public function save(Order $order, bool $flush = false): void
    {
        $this->saveWasCalled = true;
        $this->lastSavedOrder = $order;
        $this->orders[$order->externalId . '-' . $order->marketplace] = $order;
    }
}
