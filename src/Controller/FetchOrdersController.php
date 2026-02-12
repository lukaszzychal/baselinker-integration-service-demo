<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\FetchOrdersUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders/fetch', name: 'api_orders_fetch', methods: ['POST'])]
class FetchOrdersController extends AbstractController
{
    public function __construct(
        private readonly FetchOrdersUseCaseInterface $useCase,
    ) {
    }

    public function __invoke(
        #[\Symfony\Component\HttpKernel\Attribute\MapRequestPayload]
        \App\DTO\FetchOrdersRequest $requestPayload
    ): JsonResponse {
        $this->useCase->execute($requestPayload);

        return new JsonResponse(['status' => 'Order fetch job dispatched'], Response::HTTP_ACCEPTED);
    }
}
