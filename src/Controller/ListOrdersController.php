<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders', name: 'api_orders_list', methods: ['GET'])]
class ListOrdersController extends AbstractController
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $orders = $this->repository->findAll();

        return new JsonResponse([
            'orders' => array_map(
                fn (Order $order) => $this->orderToArray($order),
                $orders
            ),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function orderToArray(Order $order): array
    {
        return [
            'id' => $order->id,
            'external_id' => $order->externalId,
            'marketplace' => $order->marketplace,
            'customer_name' => $order->customerName,
            'total_amount' => $order->totalAmount,
            'created_at' => $order->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
