<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\Id;

interface GymRepository
{
    public function save(Gym $gym): void;

    public function get(Id $id): Gym;

    /**
     * @return array<Gym>
     */
    public function findAllByOwnerId(Id $ownerId): array;

    public function delete(Id $id): void;
}
