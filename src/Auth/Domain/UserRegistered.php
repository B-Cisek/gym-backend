<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\Id;

final readonly class UserRegistered
{
    public function __construct(public Id $userId) {}
}
