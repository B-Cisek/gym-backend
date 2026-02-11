<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\Id;

interface OwnerRepository
{
    public function save(Owner $owner): void;

    public function get(Id $id): Owner;

    public function getByUserId(Id $userId): Owner;

    public function existsByUserId(Id $userId): bool;
}
