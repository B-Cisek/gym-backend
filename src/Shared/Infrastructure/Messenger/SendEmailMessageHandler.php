<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Messenger;

use App\Shared\Application\Message\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final readonly class SendEmailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private string $senderEmail,
        private string $senderName,
    ) {
    }

    public function __invoke(SendEmailMessage $message): void
    {
        $email = new Email()
            ->from(sprintf('%s <%s>', $this->senderName, $this->senderEmail))
            ->to($message->recipient)
            ->subject($message->subject)
            ->text($message->body);

        $this->mailer->send($email);
    }
}
