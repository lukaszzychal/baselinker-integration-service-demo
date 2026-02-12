<?php

declare(strict_types=1);

namespace App\Tests\Unit\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use App\Integration\Decorator\RateLimitingClientDecorator;
use App\Integration\Exception\RateLimitExceededException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class RateLimitingClientDecoratorTest extends TestCase
{
    private InMemoryStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new InMemoryStorage();
    }

    public function testDelegatesCallWhenWithinLimit(): void
    {
        $inner = $this->createMock(BaselinkerClientInterface::class);
        $inner->expects($this->once())
            ->method('getOrders')
            ->willReturn(['status' => 'SUCCESS', 'orders' => []]);

        $decorator = new RateLimitingClientDecorator(
            $inner,
            $this->createLimiter(100),
        );

        $result = $decorator->getOrders(new \DateTimeImmutable());

        $this->assertSame(['status' => 'SUCCESS', 'orders' => []], $result);
    }

    public function testThrowsExceptionWhenRateLimitExceeded(): void
    {
        $inner = $this->createMock(BaselinkerClientInterface::class);
        $inner->expects($this->exactly(5))
            ->method('getOrders')
            ->willReturn(['status' => 'SUCCESS', 'orders' => []]);

        $decorator = new RateLimitingClientDecorator(
            $inner,
            $this->createLimiter(5),
        );

        $from = new \DateTimeImmutable();

        // Consume all 5 tokens
        for ($i = 0; $i < 5; ++$i) {
            $decorator->getOrders($from);
        }

        // 6th call should throw
        $this->expectException(RateLimitExceededException::class);
        $this->expectExceptionMessageMatches('/rate limit/i');

        $decorator->getOrders($from);
    }

    public function testMultipleCallsWithinLimitSucceed(): void
    {
        $inner = $this->createMock(BaselinkerClientInterface::class);
        $inner->expects($this->exactly(3))
            ->method('getOrders')
            ->willReturn(['status' => 'SUCCESS', 'orders' => []]);

        $decorator = new RateLimitingClientDecorator(
            $inner,
            $this->createLimiter(100),
        );

        $from = new \DateTimeImmutable();

        for ($i = 0; $i < 3; ++$i) {
            $result = $decorator->getOrders($from);
            $this->assertSame(['status' => 'SUCCESS', 'orders' => []], $result);
        }
    }

    private function createLimiter(int $limit): RateLimiterFactory
    {
        return new RateLimiterFactory(
            [
                'id' => 'baselinker_api',
                'policy' => 'fixed_window',
                'limit' => $limit,
                'interval' => '60 seconds',
            ],
            $this->storage,
        );
    }
}
