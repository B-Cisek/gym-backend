<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\DomainException;

final class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("User with email {$email} already exists in the system");
    }

    public function getHttpStatusCode(): int
    {
        return 409;
    }
}
