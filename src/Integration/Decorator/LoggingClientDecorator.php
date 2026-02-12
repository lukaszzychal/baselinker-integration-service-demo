<?php

declare(strict_types=1);

namespace App\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use Psr\Log\LoggerInterface;

class LoggingClientDecorator implements BaselinkerClientInterface
{
    public function __construct(
        private BaselinkerClientInterface $client,
        private LoggerInterface $logger
    ) {
    }

    public function getOrders(\DateTimeInterface $from, array $filters = []): array
    {
        $this->logger->info('Fetching orders from Baselinker', [
            'from' => $from->format('Y-m-d H:i:s'),
            'filters' => $filters,
        ]);

        $result = $this->client->getOrders($from, $filters);

        $this->logger->info('Orders fetched successfully', [
            'count' => \count($result['orders'] ?? []),
            'from' => $from->format('Y-m-d H:i:s'),
        ]);

        return $result;
    }

    public function getOrderSources(): array
    {
        $this->logger->info('Fetching order sources from Baselinker');

        $result = $this->client->getOrderSources();

        $this->logger->info('Order sources fetched successfully');

        return $result;
    }

    public function getOrderStatusList(): array
    {
        $this->logger->info('Fetching order status list from Baselinker');

        $result = $this->client->getOrderStatusList();

        $this->logger->info('Order status list fetched successfully');

        return $result;
    }
}
