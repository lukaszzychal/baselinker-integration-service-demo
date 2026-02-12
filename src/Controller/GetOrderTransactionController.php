<?php

declare(strict_types=1);

namespace App\Controller;

use App\Integration\BaselinkerClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders/{id}/transaction', name: 'api_orders_transaction', methods: ['GET'], requirements: ['id' => '\d+'])]
class GetOrderTransactionController extends AbstractController
{
    public function __construct(
        private readonly BaselinkerClientInterface $client,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $result = $this->client->getOrderTransactionData($id);

        return new JsonResponse($result);
    }
}
