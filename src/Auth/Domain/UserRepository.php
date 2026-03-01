<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\Id;

interface UserRepository
{
    public function save(User $user): void;

    public function get(Id $id): User;

    public function getByEmail(Email $email): User;

    public function existsByEmail(Email $email): bool;
}
