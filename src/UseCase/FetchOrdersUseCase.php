<?php

declare(strict_types=1);

namespace App\UseCase;

use App\DTO\FetchOrdersRequest;
use App\Factory\FetchOrdersMessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class FetchOrdersUseCase implements FetchOrdersUseCaseInterface
{
    public function __construct(
        private FetchOrdersMessageFactory $messageFactory,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function execute(FetchOrdersRequest $request): void
    {
        $message = $this->messageFactory->createFromRequest($request);
        $this->messageBus->dispatch($message);
    }
}
