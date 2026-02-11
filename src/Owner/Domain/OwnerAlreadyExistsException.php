<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\DomainException;

final class OwnerAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Owner already exists for this user');
    }

    public function getHttpStatusCode(): int
    {
        return 409;
    }
}
