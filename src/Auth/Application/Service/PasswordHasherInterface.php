<?php

declare(strict_types=1);

namespace App\Auth\Application\Service;

interface PasswordHasherInterface
{
    public function hash(string $password): string;

    public function verify(string $password, string $hashedPassword): bool;
}
