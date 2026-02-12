<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ListOrderStatusesController;
use App\Tests\Doubles\BaselinkerClientStub;
use PHPUnit\Framework\TestCase;

class ListOrderStatusesControllerTest extends TestCase
{
    public function testInvokeReturnsMappedStatuses(): void
    {
        $client = new BaselinkerClientStub(
            orderStatusListResponse: [
                'status' => 'SUCCESS',
                'statuses' => [
                    ['id' => 1, 'name' => 'New', 'name_for_customer' => 'New order', 'color' => '#00ff00'],
                    ['id' => 2, 'name' => 'Shipped', 'name_for_customer' => 'Shipped', 'color' => '#0000ff'],
                ],
            ],
        );

        $controller = new ListOrderStatusesController($client);
        $response = $controller->__invoke();

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('statuses', $data);
        $this->assertCount(2, $data['statuses']);

        $this->assertSame(1, $data['statuses'][0]['id']);
        $this->assertSame('New', $data['statuses'][0]['name']);
        $this->assertSame('New order', $data['statuses'][0]['name_for_customer']);
        $this->assertSame('#00ff00', $data['statuses'][0]['color']);

        $this->assertSame(2, $data['statuses'][1]['id']);
        $this->assertSame('Shipped', $data['statuses'][1]['name']);
    }

    public function testInvokeReturnsEmptyOnNoStatuses(): void
    {
        $client = new BaselinkerClientStub(
            orderStatusListResponse: [
                'status' => 'SUCCESS',
                'statuses' => [],
            ],
        );

        $controller = new ListOrderStatusesController($client);
        $response = $controller->__invoke();

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response->getContent(), true);

        $this->assertSame([], $data['statuses']);
    }
}
