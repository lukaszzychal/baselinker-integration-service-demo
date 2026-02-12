<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use Ackintosh\Ganesha\Exception\RejectedException;
use App\EventSubscriber\RejectedExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class RejectedExceptionSubscriberTest extends TestCase
{
    public function testMapsRejectedExceptionTo503(): void
    {
        $subscriber = new RejectedExceptionSubscriber();
        $exception = RejectedException::withServiceName('baselinker');
        $request = new \Symfony\Component\HttpFoundation\Request();
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $raw = $response->getContent();
        $this->assertIsString($raw);
        $content = json_decode($raw, true);
        $this->assertSame('service_unavailable', $content['error']);
        $this->assertArrayHasKey('message', $content);
    }

    public function testDoesNotHandleOtherExceptions(): void
    {
        $subscriber = new RejectedExceptionSubscriber();
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new \Symfony\Component\HttpFoundation\Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new \RuntimeException('Other error'),
        );

        $subscriber->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testSubscribesToKernelException(): void
    {
        $events = RejectedExceptionSubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $events);
    }
}
