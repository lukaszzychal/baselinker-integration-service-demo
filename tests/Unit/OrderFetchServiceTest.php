<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Integration\BaselinkerClientInterface;
use App\Integration\OrderMapper;
use App\Service\OrderFetchService;
use PHPUnit\Framework\TestCase;

final class OrderFetchServiceTest extends TestCase
{
    public function testFetchesAndMapsOrders(): void
    {
        // 1. Prepare Mock Client
        $client = $this->createMock(BaselinkerClientInterface::class);
        $client->expects($this->once())
            ->method('getOrders')
            ->willReturn([
                'status' => 'SUCCESS',
                'orders' => [
                    [
                        'order_id' => '100',
                        'order_source' => 'allegro',
                        'products' => [],
                    ],
                    [
                        'order_id' => '200',
                        'order_source' => 'ebay',
                        'products' => [],
                    ],
                ],
            ]);

        // 2. Prepare Real Mapper
        $mapper = new OrderMapper();

        // 3. Create Service
        $service = new OrderFetchService($client, $mapper);

        // 4. Act
        $result = $service->fetchOrders(new \DateTimeImmutable());

        // 5. Assert
        $this->assertCount(2, $result);

        $this->assertEquals('100', $result[0]->externalId);
        $this->assertEquals('allegro', $result[0]->marketplace);

        $this->assertEquals('200', $result[1]->externalId);
        $this->assertEquals('ebay', $result[1]->marketplace);
    }

    public function testHandlesEmptyResponseGracefully(): void
    {
        $client = $this->createMock(BaselinkerClientInterface::class);
        $client->expects($this->once())
            ->method('getOrders')
            ->willReturn(['status' => 'SUCCESS', 'orders' => []]);

        $service = new OrderFetchService($client, new OrderMapper());
        $result = $service->fetchOrders(new \DateTimeImmutable());

        $this->assertEmpty($result);
    }
}
