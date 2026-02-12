<?php

declare(strict_types=1);

namespace App\UseCase;

use App\DTO\FetchOrdersRequest;

interface FetchOrdersUseCaseInterface
{
    public function execute(FetchOrdersRequest $request): void;
}
