<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Listener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ExceptionListener
{
    public function __construct(private string $environment) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // TODO: Implement exception handling
    }
}
