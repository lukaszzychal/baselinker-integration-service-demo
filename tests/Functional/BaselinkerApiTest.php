<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Integration\BaselinkerClientInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[Group('external')]
class BaselinkerApiTest extends KernelTestCase
{
    public function testConnectsToRealApi(): void
    {
        self::bootKernel();
        $client = static::getContainer()->get(BaselinkerClientInterface::class);
        $this->assertInstanceOf(BaselinkerClientInterface::class, $client);
        /** @var BaselinkerClientInterface $client */
        $orders = $client->getOrders(new \DateTimeImmutable('-1 hour'));

        $this->assertEquals('SUCCESS', $orders['status']);
    }
}
