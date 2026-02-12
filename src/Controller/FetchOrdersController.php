<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\FetchOrders;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/fetch-orders', name: 'api_fetch_orders', methods: ['POST'])]
class FetchOrdersController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $dateFrom = $data['from'] ?? null;
        $marketplace = $data['marketplace'] ?? 'all';

        if (!$dateFrom) {
            // Default to yesterday if not provided? Or require it?
            // Command defaults to -1 day. Controller can do the same.
            $from = new \DateTimeImmutable('-1 day');
        } else {
            try {
                $from = new \DateTimeImmutable($dateFrom);
            } catch (\Exception $e) {
                 return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
            }
        }

        $this->messageBus->dispatch(new FetchOrders(
            from: $from,
            marketplace: (string) $marketplace
        ));

        return new JsonResponse(['status' => 'Order fetch job dispatched'], Response::HTTP_ACCEPTED);
    }
}
