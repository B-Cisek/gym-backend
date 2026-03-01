<?php

declare(strict_types=1);

namespace App\Gym\Domain;

use App\Shared\Domain\Address;
use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'gyms')]
#[Index(name: 'UNIQ_GYM_ID', columns: ['id'])]
#[Index(name: 'UNIQ_GYM_OWNER_ID', columns: ['owner_id'])]
final class Gym
{
    use TimestampTrait;

    private function __construct(
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        public Id $id,
        #[Column(name: 'owner_id', type: 'id')]
        public Id $ownerId,
        #[Column(type: Types::STRING, length: 255)]
        public string $name,
        #[Embedded(class: Address::class)]
        public Address $address,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Id $id,
        Id $ownerId,
        string $name,
        Address $address,
    ): self {
        return new self($id, $ownerId, $name, $address);
    }

    public static function restore(
        Id $id,
        Id $ownerId,
        string $name,
        Address $address,
    ): self {
        return new self($id, $ownerId, $name, $address);
    }

    public function update(string $name, Address $address): self
    {
        $this->name = $name;
        $this->address = $address;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
