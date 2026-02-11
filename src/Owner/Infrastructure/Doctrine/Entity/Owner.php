<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Doctrine\Entity;

use App\Shared\Infrastructure\Doctrine\Embeddable\Address;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'owners')]
#[UniqueConstraint(name: 'UNIQ_OWNER_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_OWNER_USER_ID', columns: ['user_id'])]
class Owner
{
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[Column(name: 'user_id', type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $userId,
        #[Embedded(class: Address::class)]
        private Address $address,
        #[Column(type: Types::STRING, length: 255, nullable: true)]
        private ?string $companyName = null,
        #[Column(type: Types::STRING, length: 10, nullable: true)]
        private ?string $taxId = null,
        #[Column(type: Types::STRING, length: 20, nullable: true)]
        private ?string $phone = null,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): Owner
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): Owner
    {
        $this->taxId = $taxId;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): Owner
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): Owner
    {
        $this->address = $address;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): Owner
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
