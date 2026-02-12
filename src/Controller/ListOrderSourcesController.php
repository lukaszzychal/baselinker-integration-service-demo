<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\OrderSourceDTO;
use App\Integration\BaselinkerClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/order-sources', name: 'api_order_sources', methods: ['GET'])]
class ListOrderSourcesController extends AbstractController
{
    public function __construct(
        private readonly BaselinkerClientInterface $client,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $result = $this->client->getOrderSources();

        $sources = $this->mapSources($result['sources'] ?? []);

        return new JsonResponse(['sources' => array_map(
            fn (OrderSourceDTO $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'type' => $s->type,
            ],
            $sources,
        )]);
    }

    /**
     * Baselinker returns sources grouped by type: { "personal": { "0": "Personal" }, "shop": { ... }, "marketplace": { ... } }.
     *
     * @param array<string, array<string|int, string>> $grouped
     *
     * @return array<int, OrderSourceDTO>
     */
    private function mapSources(array $grouped): array
    {
        $sources = [];

        foreach ($grouped as $type => $items) {
            foreach ($items as $id => $name) {
                $sources[] = new OrderSourceDTO(
                    id: (int) $id,
                    name: (string) $name,
                    type: (string) $type,
                );
            }
        }

        return $sources;
    }
}
