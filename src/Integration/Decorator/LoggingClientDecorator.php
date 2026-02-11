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

    public function getOrders(\DateTimeInterface $from): array
    {
        $this->logger->info('Fetching orders from Baselinker', ['from' => $from->format('Y-m-d H:i:s')]);

        $result = $this->client->getOrders($from);

        $this->logger->info('Orders fetched successfully', [
            'count' => \count($result['orders'] ?? []),
            'from' => $from->format('Y-m-d H:i:s'),
        ]);

        return $result;
    }
}
