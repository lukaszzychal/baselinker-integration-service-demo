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
        $marketplaces = $this->parseMarketplaces($request->filter_order_source);

        foreach ($marketplaces as $marketplace) {
            $singleRequest = $request->withFilterOrderSource($marketplace);
            $message = $this->messageFactory->createFromRequest($singleRequest);
            $this->messageBus->dispatch($message);
        }
    }

    /**
     * Splits comma-separated marketplace names into individual values.
     * Returns [null] if no filter is specified (fetch from all sources).
     *
     * @return array<int, string|null>
     */
    private function parseMarketplaces(?string $filterOrderSource): array
    {
        if (null === $filterOrderSource) {
            return [null];
        }

        $marketplaces = array_map('trim', explode(',', $filterOrderSource));
        $marketplaces = array_filter($marketplaces, fn (string $v): bool => '' !== $v);

        return [] === $marketplaces ? [null] : array_values($marketplaces);
    }
}
