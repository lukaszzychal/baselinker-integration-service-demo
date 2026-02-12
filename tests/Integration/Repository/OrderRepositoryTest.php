<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    private ?OrderRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->repository = $entityManager->getRepository(Order::class);

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
        $this->assertNotNull($foundOrder->createdAt);
    }

    public function testFindByExternalIdReturnsNullWhenNotFound(): void
    {
        $foundOrder = $this->repository->findByExternalId('non-existent', 'amazon');

        $this->assertNull($foundOrder);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        // Clear database if necessary, usually handled by DAMADoctrineTestBundle
    }
}
