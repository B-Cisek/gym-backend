<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class InvalidIdException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("Invalid identifier: {$id}");
    }

    public function getHttpStatusCode(): int
    {
        return 400;
    }
}
