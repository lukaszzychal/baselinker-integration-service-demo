<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    private OrderRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $repository = $container->get(OrderRepository::class);
        $this->assertInstanceOf(OrderRepository::class, $repository);
        $this->repository = $repository;

        // Optional: truncate table if not using transaction rollback
        // $this->truncateEntities([Order::class]);
    }

    public function testFindByExternalIdReturnsCorrectOrder(): void
    {
        $externalId = 'ORDER-123';
        $marketplace = 'amazon';
        $customerName = 'John Doe';
        $totalAmount = '100.00';

        $order = new Order(
            $externalId,
            $marketplace,
            $customerName,
            $totalAmount
        );

        $this->repository->save($order, true);

        $foundOrder = $this->repository->findByExternalId($externalId, $marketplace);

        $this->assertNotNull($foundOrder);
        $this->assertSame($externalId, $foundOrder->externalId);
        $this->assertSame($marketplace, $foundOrder->marketplace);
        $this->assertSame($customerName, $foundOrder->customerName);
        $this->assertSame($totalAmount, $foundOrder->totalAmount);
        $this->assertNotNull($foundOrder->id);
    }

    public function testFindByExternalIdReturnsNullWhenNotFound(): void
    {
        $foundOrder = $this->repository->findByExternalId('non-existent', 'amazon');

        $this->assertNull($foundOrder);
    }

    public function testFindByIdReturnsCorrectOrder(): void
    {
        $order = new Order('ext-456', 'allegro', 'Test User', '99.99');
        $this->repository->save($order, true);

        $foundOrder = $this->repository->findById((int) $order->id);

        $this->assertNotNull($foundOrder);
        $this->assertSame($order->id, $foundOrder->id);
        $this->assertSame('ext-456', $foundOrder->externalId);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $foundOrder = $this->repository->findById(99999);

        $this->assertNull($foundOrder);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clear database if necessary, usually handled by DAMADoctrineTestBundle
    }
}
