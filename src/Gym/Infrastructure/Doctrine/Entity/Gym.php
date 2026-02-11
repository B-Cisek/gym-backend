<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Doctrine\Entity;

use App\Shared\Infrastructure\Doctrine\Embeddable\Address;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'gyms')]
#[Index(name: 'UNIQ_GYM_ID', columns: ['id'])]
#[Index(name: 'UNIQ_GYM_OWNER_ID', columns: ['owner_id'])]
class Gym
{
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[Column(name: 'owner_id', type: UuidType::NAME, length: 36)]
        private Uuid $ownerId,
        #[Column(type: Types::STRING, length: 255)]
        private string $name,
        #[Embedded(class: Address::class)]
        private Address $address,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): Gym
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOwnerId(): Uuid
    {
        return $this->ownerId;
    }

    public function setOwnerId(Uuid $ownerId): Gym
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Gym
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): Gym
    {
        $this->address = $address;

        return $this;
    }
}
