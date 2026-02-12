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
        private \App\Repository\OrderRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FetchOrders $message): void
    {
        $filters = $message->filters;

        if ('all' !== $message->marketplace) {
            $filters['filter_order_source'] = $message->marketplace;
        }

        $result = $this->client->getOrders($message->from, $filters);

        /** @var array<int, array<string, mixed>> $orders */
        $orders = $result['orders'] ?? [];

        $dtos = array_map(
            fn (array $raw) => $this->mapper->map($raw),
            $orders
        );

        foreach ($dtos as $dto) {
            if ($this->repository->findByExternalId($dto->externalId, $dto->marketplace)) {
                continue;
            }

            $order = new \App\Entity\Order(
                externalId: $dto->externalId,
                marketplace: $dto->marketplace,
                customerName: $dto->customerName,
                totalAmount: number_format($dto->totalAmount / 100, 2, '.', ''),
                createdAt: $dto->createdAt,
            );

            $this->repository->save($order, true);
        }
    }
}
