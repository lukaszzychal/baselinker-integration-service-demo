<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\OrderStatusDTO;
use App\Integration\BaselinkerClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/order-statuses', name: 'api_order_statuses', methods: ['GET'])]
class ListOrderStatusesController extends AbstractController
{
    public function __construct(
        private readonly BaselinkerClientInterface $client,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $result = $this->client->getOrderStatusList();

        $statuses = $this->mapStatuses($result['statuses'] ?? []);

        return new JsonResponse(['statuses' => array_map(
            fn (OrderStatusDTO $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'name_for_customer' => $s->nameForCustomer,
                'color' => $s->color,
            ],
            $statuses,
        )]);
    }

    /**
     * @param array<int, array<string, mixed>> $rawStatuses
     *
     * @return array<int, OrderStatusDTO>
     */
    private function mapStatuses(array $rawStatuses): array
    {
        return array_map(
            fn (array $raw) => new OrderStatusDTO(
                id: (int) ($raw['id'] ?? 0),
                name: (string) ($raw['name'] ?? ''),
                nameForCustomer: (string) ($raw['name_for_customer'] ?? ''),
                color: (string) ($raw['color'] ?? ''),
            ),
            $rawStatuses,
        );
    }
}
