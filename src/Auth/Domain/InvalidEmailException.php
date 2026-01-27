<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\DomainException;

final class InvalidEmailException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email address: {$email}");
    }

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
