<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mailer;

use App\Shared\Application\Message\SendEmailMessage;
use App\Shared\Application\Service\Email;
use App\Shared\Application\Service\Mailer;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyMailer implements Mailer
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function send(Email $email): void
    {
        $message = new SendEmailMessage(
            recipient: $email->recipient,
            subject: $email->subject,
            body: $email->text,
        );

        $this->messageBus->dispatch($message);
    }
}
