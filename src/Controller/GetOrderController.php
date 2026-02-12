<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders/{id}', name: 'api_orders_show', methods: ['GET'], requirements: ['id' => '\d+'])]
class GetOrderController extends AbstractController
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $order = $this->repository->findById($id);

        if (null === $order) {
            return new JsonResponse(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $order->id,
            'external_id' => $order->externalId,
            'marketplace' => $order->marketplace,
            'customer_name' => $order->customerName,
            'total_amount' => $order->totalAmount,
            'created_at' => $order->createdAt->format(\DateTimeInterface::ATOM),
        ]);
    }
}
