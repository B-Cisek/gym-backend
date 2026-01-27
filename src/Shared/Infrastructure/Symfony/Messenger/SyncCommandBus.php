<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Command\Sync\Command;
use App\Shared\Application\Command\Sync\CommandBus;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class SyncCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $commandSyncBus) {}

    public function dispatch(Command $command): mixed
    {
        try {
            $envelope = $this->commandSyncBus->dispatch($command);
            $handledStamp = $envelope->last(HandledStamp::class);

            return $handledStamp?->getResult();
        } catch (ExceptionInterface $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
