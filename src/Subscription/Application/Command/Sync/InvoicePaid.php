<?php

declare(strict_types=1);

namespace App\Subscription\Application\Command\Sync;

use App\Shared\Application\Command\Sync\Command;
use App\Subscription\Application\Dto\WebhookEvent;

final readonly class InvoicePaid implements Command
{
    public function __construct(public WebhookEvent $event) {}
}
