<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\DomainException;

final class InvalidAddressException extends DomainException
{
    public function __construct(string $field)
    {
        parent::__construct("Invalid address: {$field} cannot be empty");
    }

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
