<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Embeddable;

use App\Shared\Domain\Address as DomainAddress;
use App\Shared\Domain\Voivodeship;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Address
{
    public function __construct(
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        private ?string $street = null,
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        private ?string $city = null,
        #[Column(type: Types::STRING, length: 6, nullable: true)]
        private ?string $postalCode = null,
        #[Column(type: Types::STRING, length: 50, nullable: true, enumType: Voivodeship::class)]
        private ?Voivodeship $voivodeship = null,
    ) {}

    public static function fromDomain(DomainAddress $address): self
    {
        return new self(
            street: $address->street,
            city: $address->city,
            postalCode: $address->postalCode,
            voivodeship: $address->voivodeship,
        );
    }

    public function toDomain(): DomainAddress
    {
        return new DomainAddress(
            street: $this->street,
            city: $this->city,
            postalCode: $this->postalCode,
            voivodeship: $this->voivodeship,
        );
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getVoivodeship(): ?Voivodeship
    {
        return $this->voivodeship;
    }
}
