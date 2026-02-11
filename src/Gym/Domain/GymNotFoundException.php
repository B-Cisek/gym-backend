<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\DomainException;

final class GymNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Gym not found');
    }

    public function getHttpStatusCode(): int
    {
        return 404;
    }
}
