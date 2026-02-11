<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\DomainException;

final class InvalidTaxIdException extends DomainException
{
    public function __construct(string $taxId)
    {
        parent::__construct("Invalid tax ID: \"{$taxId}\"");
    }

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
