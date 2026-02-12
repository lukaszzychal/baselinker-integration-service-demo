<?php

declare(strict_types=1);

namespace App\Integration\Exception;

/**
 * Base exception for errors returned by or related to the Baselinker API.
 */
class BaselinkerApiException extends \RuntimeException
{
    public function __construct(
        string $message = 'Baselinker API error.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
