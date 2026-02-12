<?php

declare(strict_types=1);

namespace App\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Decorator that records performance metrics (duration, success/failure) for each Baselinker API call.
 * Logs to the "metrics" channel for monitoring and observability.
 */
final class MetricsClientDecorator implements BaselinkerClientInterface
{
    private const LOG_KEY = 'baselinker_api_call';

    public function __construct(
        private readonly BaselinkerClientInterface $client,
        private readonly LoggerInterface $metricsLogger,
    ) {
    }

    /** @return array<string, mixed> */
    public function getOrders(\DateTimeInterface $from, array $filters = []): array
    {
        return $this->measure('getOrders', fn () => $this->client->getOrders($from, $filters));
    }

    /** @return array<string, mixed> */
    public function getOrderSources(): array
    {
        return $this->measure('getOrderSources', fn () => $this->client->getOrderSources());
    }

    /** @return array<string, mixed> */
    public function getOrderStatusList(): array
    {
        return $this->measure('getOrderStatusList', fn () => $this->client->getOrderStatusList());
    }

    /** @return array<string, mixed> */
    public function getOrderTransactionData(int $orderId): array
    {
        return $this->measure('getOrderTransactionData', fn () => $this->client->getOrderTransactionData($orderId));
    }

    /**
     * @template T
     *
     * @param callable(): T $callable
     *
     * @return T
     */
    private function measure(string $method, callable $callable): mixed
    {
        $start = microtime(true);

        try {
            $result = $callable();
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->metricsLogger->info(self::LOG_KEY, [
                'method' => $method,
                'duration_ms' => $durationMs,
                'status' => 'success',
            ]);

            return $result;
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $this->metricsLogger->warning(self::LOG_KEY, [
                'method' => $method,
                'duration_ms' => $durationMs,
                'status' => 'failure',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
