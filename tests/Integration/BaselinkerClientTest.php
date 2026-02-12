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

    public function testGetOrdersPassesFiltersToApi(): void
    {
        $json = (string) json_encode(['status' => 'SUCCESS', 'orders' => []]);

        /** @var array<string, mixed> $capturedOptions */
        $capturedOptions = [];

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($json, &$capturedOptions): MockResponse {
            $capturedOptions = $options;

            return new MockResponse($json);
        });

        $client = new BaselinkerClient($httpClient, 'fake-token');

        $from = new \DateTimeImmutable('2024-01-01 00:00:00');
        $client->getOrders($from, ['filter_order_source' => 'allegro', 'status_id' => 5]);

        // Symfony normalizes 'body' to 'normalized_body' or encodes it as a string
        // We need to parse the actual body content
        $body = $capturedOptions['body'] ?? '';
        if (\is_string($body)) {
            parse_str($body, $parsed);
        } else {
            $parsed = $body;
        }

        $this->assertSame('getOrders', $parsed['method'] ?? null);

        /** @var array<string, mixed> $params */
        $params = json_decode((string) ($parsed['parameters'] ?? '{}'), true);

        $this->assertSame($from->getTimestamp(), $params['date_from']);
        $this->assertSame('allegro', $params['filter_order_source']);
        $this->assertSame(5, $params['status_id']);
    }

    public function testGetOrderSourcesCallsCorrectMethod(): void
    {
        $json = (string) json_encode([
            'status' => 'SUCCESS',
            'sources' => [
                'personal' => [0 => 'Personal'],
                'marketplace' => [456 => 'Allegro'],
            ],
        ]);

        /** @var array<string, mixed> $capturedOptions */
        $capturedOptions = [];

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($json, &$capturedOptions): MockResponse {
            $capturedOptions = $options;

            return new MockResponse($json);
        });

        $client = new BaselinkerClient($httpClient, 'fake-token');
        $result = $client->getOrderSources();

        $this->assertArrayHasKey('sources', $result);

        $body = $capturedOptions['body'] ?? '';
        if (\is_string($body)) {
            parse_str($body, $parsed);
        } else {
            $parsed = $body;
        }

        $this->assertSame('getOrderSources', $parsed['method'] ?? null);
    }

    public function testGetOrderStatusListCallsCorrectMethod(): void
    {
        $json = (string) json_encode([
            'status' => 'SUCCESS',
            'statuses' => [
                ['id' => 1, 'name' => 'New', 'name_for_customer' => 'New order'],
            ],
        ]);

        /** @var array<string, mixed> $capturedOptions */
        $capturedOptions = [];

        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($json, &$capturedOptions): MockResponse {
            $capturedOptions = $options;

            return new MockResponse($json);
        });

        $client = new BaselinkerClient($httpClient, 'fake-token');
        $result = $client->getOrderStatusList();

        $this->assertArrayHasKey('statuses', $result);

        $body = $capturedOptions['body'] ?? '';
        if (\is_string($body)) {
            parse_str($body, $parsed);
        } else {
            $parsed = $body;
        }

        $this->assertSame('getOrderStatusList', $parsed['method'] ?? null);
    }
}
