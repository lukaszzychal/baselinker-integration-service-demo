<?php

declare(strict_types=1);

namespace App\Integration\Exception;

/**
 * Thrown when the Baselinker API rate limit (100 requests/minute) would be exceeded.
 */
final class RateLimitExceededException extends \RuntimeException
{
    public function __construct(
        string $message = 'Baselinker API rate limit exceeded. Try again later.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
