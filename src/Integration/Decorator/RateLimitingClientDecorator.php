<?php

declare(strict_types=1);

namespace App\Integration\Decorator;

use App\Integration\BaselinkerClientInterface;
use App\Integration\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Enforces Baselinker API rate limit (100 requests per minute).
 *
 * This decorator wraps the API client and checks the rate limiter
 * before each request. All outgoing calls (from controllers, CLI,
 * message handlers) pass through this single point of control.
 */
final class RateLimitingClientDecorator implements BaselinkerClientInterface
{
    public function __construct(
        private BaselinkerClientInterface $client,
        private RateLimiterFactory $baselinkerApiLimiter,
    ) {
    }

    /** @return array<string, mixed> */
    public function getOrders(\DateTimeInterface $from): array
    {
        $this->consume();

        return $this->client->getOrders($from);
    }

    private function consume(): void
    {
        $limiter = $this->baselinkerApiLimiter->create('baselinker_api');
        $limit = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            $seconds = $limit->getRetryAfter()->getTimestamp() - time();

            throw new RateLimitExceededException(\sprintf('Baselinker API rate limit exceeded. Retry after %d seconds.', max(1, $seconds)));
        }
    }
}
