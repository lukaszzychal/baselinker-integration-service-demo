<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Integration\BaselinkerClientInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Contract tests against real Baselinker API.
 * Run with: make test-contract (requires BASELINKER_API_TOKEN).
 */
#[Group('external')]
class BaselinkerApiTest extends KernelTestCase
{
    public function testGetOrdersContract(): void
    {
        self::bootKernel();
        $client = static::getContainer()->get(BaselinkerClientInterface::class);
        $this->assertInstanceOf(BaselinkerClientInterface::class, $client);
        /** @var BaselinkerClientInterface $client */
        $result = $client->getOrders(new \DateTimeImmutable('-1 hour'));

        $this->assertSame('SUCCESS', $result['status']);
        $this->assertArrayHasKey('orders', $result);
        $this->assertIsArray($result['orders']);
    }

    public function testGetOrderSourcesContract(): void
    {
        self::bootKernel();
        $client = static::getContainer()->get(BaselinkerClientInterface::class);
        /** @var BaselinkerClientInterface $client */
        $result = $client->getOrderSources();

        $this->assertSame('SUCCESS', $result['status']);
        $this->assertArrayHasKey('sources', $result);
        $this->assertIsArray($result['sources']);
    }

    public function testGetOrderStatusListContract(): void
    {
        self::bootKernel();
        $client = static::getContainer()->get(BaselinkerClientInterface::class);
        /** @var BaselinkerClientInterface $client */
        $result = $client->getOrderStatusList();

        $this->assertSame('SUCCESS', $result['status']);
        $this->assertArrayHasKey('statuses', $result);
        $this->assertIsArray($result['statuses']);
    }
}
