<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\DomainException;

class UserNotFoundException extends DomainException
{
    public function getHttpStatusCode(): int
    {
        return 404;
    }
}
