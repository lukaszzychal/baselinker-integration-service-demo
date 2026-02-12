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
    /** @var array<int, Order> */
    private array $ordersById = [];
    private int $nextId = 1;

    /**
     * @return list<Order>
     */
    public function findAll(): array
    {
        return array_values($this->ordersById);
    }

    public function findById(int $id): ?Order
    {
        return $this->ordersById[$id] ?? null;
    }

    public function findByExternalId(string $externalId, string $marketplace): ?Order
    {
        return $this->orders[$externalId . '-' . $marketplace] ?? null;
    }

    public function save(Order $order, bool $flush = false): void
    {
        $this->saveWasCalled = true;
        $this->lastSavedOrder = $order;

        $key = $order->externalId . '-' . $order->marketplace;
        if (null === $order->id) {
            $id = $this->nextId++;
            $reflection = new \ReflectionProperty(Order::class, 'id');
            $reflection->setValue($order, $id);
            $this->ordersById[$id] = $order;
        }
        $this->orders[$key] = $order;
    }
}
