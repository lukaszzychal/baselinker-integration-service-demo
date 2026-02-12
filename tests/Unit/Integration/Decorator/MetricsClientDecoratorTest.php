<?php

declare(strict_types=1);

namespace App\Tests\Unit\Integration\Decorator;

use App\Integration\Decorator\MetricsClientDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

final class MetricsClientDecoratorTest extends TestCase
{
    public function testDelegatesAndLogsSuccess(): void
    {
        $logCollector = new class extends AbstractLogger {
            /** @var array<int, array<string, mixed>> */
            public array $entries = [];

            public function log(mixed $level, string|\Stringable $message, array $context = []): void
            {
                $this->entries[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
            }
        };

        $inner = new \App\Tests\Doubles\BaselinkerClientStub(
            orderSourcesResponse: ['status' => 'SUCCESS', 'sources' => []],
        );

        $decorator = new MetricsClientDecorator($inner, $logCollector);

        $result = $decorator->getOrderSources();

        $this->assertSame(['status' => 'SUCCESS', 'sources' => []], $result);
        $this->assertCount(1, $logCollector->entries);
        $this->assertSame('info', $logCollector->entries[0]['level']);
        $this->assertSame('baselinker_api_call', $logCollector->entries[0]['message']);
        $this->assertSame('getOrderSources', $logCollector->entries[0]['context']['method']);
        $this->assertSame('success', $logCollector->entries[0]['context']['status']);
        $this->assertArrayHasKey('duration_ms', $logCollector->entries[0]['context']);
    }

    public function testLogsFailureAndRethrows(): void
    {
        $logCollector = new class extends AbstractLogger {
            /** @var array<int, array<string, mixed>> */
            public array $entries = [];

            public function log(mixed $level, string|\Stringable $message, array $context = []): void
            {
                $this->entries[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
            }
        };

        $inner = $this->createMock(\App\Integration\BaselinkerClientInterface::class);
        $inner->method('getOrderSources')->willThrowException(new \RuntimeException('Network error'));

        $decorator = new MetricsClientDecorator($inner, $logCollector);

        try {
            $decorator->getOrderSources();
            $this->fail('Expected exception');
        } catch (\RuntimeException $e) {
            $this->assertSame('Network error', $e->getMessage());
        }

        $this->assertCount(1, $logCollector->entries);
        $this->assertSame('warning', $logCollector->entries[0]['level']);
        $this->assertSame('failure', $logCollector->entries[0]['context']['status']);
        $this->assertSame('Network error', $logCollector->entries[0]['context']['error']);
    }
}
