<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Utils;

use App\Shared\Domain\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyEventDispatcher implements EventDispatcher
{
    public function __construct(
        private EventDispatcherInterface $symfonyDispatcher
    ) {
    }

    public function dispatch(object $event, ?string $eventName = null): void
    {
        $this->symfonyDispatcher->dispatch($event, $eventName);
    }
}
