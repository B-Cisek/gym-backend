<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
final class Address
{
    public function __construct(
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        public ?string $street = null,
        #[Column(type: Types::STRING, length: 100, nullable: true)]
        public ?string $city = null,
        #[Column(type: Types::STRING, length: 6, nullable: true)]
        public ?string $postalCode = null,
    ) {}
}
