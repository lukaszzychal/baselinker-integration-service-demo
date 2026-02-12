<?php

declare(strict_types=1);

namespace App\Integration\Exception;

/**
 * Thrown when the Baselinker API response has an unexpected structure (e.g. missing required keys).
 */
final class InvalidResponseException extends BaselinkerApiException
{
    public function __construct(
        string $message = 'Invalid Baselinker API response structure.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
