<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Command\Async\Command;
use App\Shared\Application\Command\Async\CommandBus;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class AsyncCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $commandAsyncBus) {}

    public function dispatch(Command $command): void
    {
        try {
            $this->commandAsyncBus->dispatch($command);
        } catch (ExceptionInterface $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
