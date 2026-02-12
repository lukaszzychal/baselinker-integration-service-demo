<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Ackintosh\Ganesha\Exception\RejectedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * When Circuit Breaker (Ganesha) is open, RejectedException is thrown.
 * Map it to 503 Service Unavailable for API consumers.
 */
final class RejectedExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if (!$throwable instanceof RejectedException) {
            return;
        }

        $event->setResponse(new JsonResponse([
            'error' => 'service_unavailable',
            'message' => 'Baselinker API is temporarily unavailable. Please try again later.',
        ], Response::HTTP_SERVICE_UNAVAILABLE));
    }
}
