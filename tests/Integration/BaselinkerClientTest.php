<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Integration\BaselinkerClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class BaselinkerClientTest extends TestCase
{
    public function testGetOrdersReturnsArray(): void
    {
        $json = json_encode([
            'status' => 'SUCCESS',
            'orders' => [
                ['order_id' => '123', 'user_login' => 'john.doe'],
            ],
        ]);
        $mockResponse = new MockResponse((string) $json);

        $httpClient = new MockHttpClient($mockResponse);
        $client = new BaselinkerClient($httpClient, 'fake-token');

        $result = $client->getOrders(new \DateTimeImmutable('-1 day'));

        $this->assertArrayHasKey('orders', $result);
        $this->assertCount(1, $result['orders']);
    }
}
