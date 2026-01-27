<?php

declare(strict_types=1);

namespace App\Shared\Application\Message;

final readonly class SendEmailMessage
{
    public function __construct(
        public string $recipient,
        public string $subject,
        public string $body,
    ) {
    }
}
