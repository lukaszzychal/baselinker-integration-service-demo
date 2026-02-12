<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\FetchOrders;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class FetchOrdersHandler
{
    public function __construct(
        private \App\Integration\BaselinkerClientInterface $client,
        private \App\Integration\OrderMapper $mapper,
    ) {
    }

    public function __invoke(FetchOrders $message): void
    {
        $result = $this->client->getOrders($message->from);

        /** @var array<int, array<string, mixed>> $orders */
        $orders = $result['orders'] ?? [];

        $dtos = array_map(
            fn (array $raw) => $this->mapper->map($raw),
            $orders
        );
    }
}
