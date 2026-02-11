<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Integration\OrderMapper;
use App\Service\OrderFetchService;
use App\Tests\Doubles\BaselinkerClientStub;
use PHPUnit\Framework\TestCase;

final class OrderFetchServiceTest extends TestCase
{
    public function testFetchesAndMapsOrders(): void
    {
        $client = new BaselinkerClientStub([
            'status' => 'SUCCESS',
            'orders' => [
                [
                    'order_id' => '100',
                    'order_source' => 'allegro',
                    'products' => [], // total 0
                ],
                [
                    'order_id' => '200',
                    'order_source' => 'ebay',
                    'products' => [
                        ['price_brutto' => 10.50, 'quantity' => 1], // total 1050
                    ],
                ],
            ],
        ]);

        $mapper = new OrderMapper();
        $service = new OrderFetchService($client, $mapper);

        $result = $service->fetchOrders(new \DateTimeImmutable());

        $this->assertCount(2, $result);

        $this->assertEquals('100', $result[0]->externalId);
        $this->assertSame(0, $result[0]->totalAmount);

        $this->assertEquals('200', $result[1]->externalId);
        $this->assertSame(1050, $result[1]->totalAmount);
    }

    public function testHandlesEmptyResponseGracefully(): void
    {
        $client = new BaselinkerClientStub(['status' => 'SUCCESS', 'orders' => []]);

        $service = new OrderFetchService($client, new OrderMapper());
        $result = $service->fetchOrders(new \DateTimeImmutable());

        $this->assertEmpty($result);
    }
}
