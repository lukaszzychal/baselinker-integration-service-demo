<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use App\Integration\OrderMapper;
use App\Message\FetchOrders;
use App\MessageHandler\FetchOrdersHandler;
use App\Tests\Doubles\BaselinkerClientStub;
use PHPUnit\Framework\TestCase;

class FetchOrdersHandlerTest extends TestCase
{
    public function testInvokeFetchesAndMapsOrders(): void
    {
        $fakeOrders = [
            'status' => 'SUCCESS',
            'orders' => [
                [
                    'order_id' => 123,
                    'date_add' => 1676121600,
                    'delivery_fullname' => 'Jan Kowalski',
                    'delivery_company' => '',
                    'delivery_address' => 'Testowa 1',
                    'delivery_city' => 'Warszawa',
                    'delivery_postcode' => '00-001',
                    'delivery_country_code' => 'PL',
                    'phone' => '123456789',
                    'email' => 'jan@example.com',
                    'total_price' => 100.50,
                    'currency' => 'PLN',
                    'payment_method' => 'cod',
                    'order_status_id' => 1,
                    'items' => [],
                ],
            ],
        ];

        $client = new BaselinkerClientStub($fakeOrders);
        $mapper = new OrderMapper();
        $repositorySpy = new \App\Tests\Doubles\OrderRepositorySpy();

        $handler = new FetchOrdersHandler($client, $mapper, $repositorySpy);

        $message = new FetchOrders(new \DateTimeImmutable('2023-01-01'));

        $handler($message);

        $this->assertTrue($repositorySpy->saveWasCalled, 'Repository save method should be called');
        $this->assertInstanceOf(\App\Entity\Order::class, $repositorySpy->lastSavedOrder);
        $this->assertEquals(123, $repositorySpy->lastSavedOrder->externalId);
    }

    public function testFiltersArePassedToClient(): void
    {
        $client = new BaselinkerClientStub(['status' => 'SUCCESS', 'orders' => []]);
        $mapper = new OrderMapper();
        $repositorySpy = new \App\Tests\Doubles\OrderRepositorySpy();

        $handler = new FetchOrdersHandler($client, $mapper, $repositorySpy);

        $message = new FetchOrders(
            from: new \DateTimeImmutable('2023-01-01'),
            marketplace: 'allegro',
            filters: ['status_id' => 5],
        );

        $handler($message);

        $this->assertSame('allegro', $client->lastFilters['filter_order_source'] ?? null);
        $this->assertSame(5, $client->lastFilters['status_id'] ?? null);
    }

    public function testMarketplaceAllDoesNotAddFilterOrderSource(): void
    {
        $client = new BaselinkerClientStub(['status' => 'SUCCESS', 'orders' => []]);
        $mapper = new OrderMapper();
        $repositorySpy = new \App\Tests\Doubles\OrderRepositorySpy();

        $handler = new FetchOrdersHandler($client, $mapper, $repositorySpy);

        $message = new FetchOrders(
            from: new \DateTimeImmutable('2023-01-01'),
            marketplace: 'all',
        );

        $handler($message);

        $this->assertArrayNotHasKey('filter_order_source', $client->lastFilters ?? []);
    }
}
