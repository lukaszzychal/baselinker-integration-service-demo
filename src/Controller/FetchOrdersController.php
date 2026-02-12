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

    public function __invoke(
        #[\Symfony\Component\HttpKernel\Attribute\MapRequestPayload]
        \App\DTO\FetchOrdersRequest $requestPayload
    ): JsonResponse
    {
        // Map DTO to Message
        // Note: FetchOrders message currently supports 'from' (DateTime) and 'marketplace' (string).
        // We map 'dateFrom' (int timestamp) or 'dateConfirmedFrom' (int timestamp) to 'from'.

        $timestamp = $requestPayload->date_from ?? $requestPayload->date_confirmed_from ?? time() - 86400;
        $from = (new \DateTimeImmutable())->setTimestamp((int)$timestamp);

        $marketplace = $requestPayload->filter_order_source ?? 'all';


        $this->messageBus->dispatch(new FetchOrders(
            from: $from,
            marketplace: (string) $marketplace
        ));

        return new JsonResponse(['status' => 'Order fetch job dispatched'], Response::HTTP_ACCEPTED);
    }
}
