<?php

declare(strict_types=1);

namespace App\Subscription\Application\Service;

use App\Subscription\Application\Dto\WebhookEvent;
use Stripe\Exception\SignatureVerificationException;

interface WebhookSignatureVerifierInterface
{
    /**
     * @throws SignatureVerificationException
     */
    public function verify(string $payload, string $signatureHeader): WebhookEvent;
}
