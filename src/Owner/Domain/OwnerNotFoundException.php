<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\DomainException;

final class OwnerNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Owner not found');
    }

    public function getHttpStatusCode(): int
    {
        return 404;
    }
}
