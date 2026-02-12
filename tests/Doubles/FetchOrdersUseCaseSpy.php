<?php

declare(strict_types=1);

namespace App\Tests\Doubles;

use App\DTO\FetchOrdersRequest;
use App\UseCase\FetchOrdersUseCaseInterface;

class FetchOrdersUseCaseSpy implements FetchOrdersUseCaseInterface
{
    public ?FetchOrdersRequest $lastRequest = null;

    /** @var array<int, FetchOrdersRequest> */
    public array $allRequests = [];

    public int $executeCount = 0;

    public function execute(FetchOrdersRequest $request): void
    {
        $this->lastRequest = $request;
        $this->allRequests[] = $request;
        ++$this->executeCount;
    }
}
