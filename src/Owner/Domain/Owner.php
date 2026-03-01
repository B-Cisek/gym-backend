<?php

declare(strict_types=1);

namespace App\Owner\Domain;

use App\Shared\Domain\Address;
use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[Entity]
#[Table(name: 'owners')]
#[UniqueConstraint(name: 'UNIQ_OWNER_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_OWNER_USER_ID', columns: ['user_id'])]
#[UniqueConstraint(name: 'UNIQ_OWNER_STRIPE_CUSTOMER_ID', columns: ['stripe_customer_id'])]
class Owner
{
    use TimestampTrait;

    private function __construct(
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        public Id $id,
        #[Column(name: 'user_id', type: 'id', unique: true)]
        public Id $userId,
        #[Column(type: Types::BOOLEAN, options: ['default' => false])]
        public bool $isProfileComplete,
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        public ?string $firstName = null,
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        public ?string $lastName = null,
        #[Column(type: Types::STRING, length: 255, nullable: true)]
        public ?string $email = null,
        #[Column(type: Types::STRING, length: 255, nullable: true)]
        public ?string $companyName = null,
        #[Column(type: Types::STRING, length: 10, nullable: true)]
        public ?string $taxId = null,
        #[Column(type: Types::STRING, length: 20, nullable: true)]
        public ?string $phone = null,
        #[Embedded(class: Address::class)]
        public ?Address $address = null,
        #[Column(type: Types::STRING, unique: true, nullable: true)]
        public ?string $stripeCustomerId = null,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Id $id,
        Id $userId,
        bool $profileCompleted = false,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
        ?string $stripeCustomerId = null,
    ): self {
        return new self($id, $userId, $profileCompleted, $firstName, $lastName, $email, $companyName, $taxId, $phone, $address, $stripeCustomerId);
    }

    public function update(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $companyName = null,
        ?string $taxId = null,
        ?string $phone = null,
        ?Address $address = null,
    ): self {
        $this->firstName = $firstName ?? $this->firstName;
        $this->lastName = $lastName ?? $this->lastName;
        $this->email = $email ?? $this->email;
        $this->companyName = $companyName ?? $this->companyName;
        $this->taxId = $taxId ?? $this->taxId;
        $this->phone = $phone ?? $this->phone;
        $this->address = $address ?? $this->address;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function withStripeCustomerId(string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function setProfileCompleted(): self
    {
        $this->isProfileComplete = true;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
