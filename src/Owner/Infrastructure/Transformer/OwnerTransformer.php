<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Transformer;

use App\Owner\Domain\Owner as DomainOwner;
use App\Owner\Infrastructure\Doctrine\Entity\Owner as OwnerEntity;
use App\Shared\Domain\Address as DomainAddress;
use App\Shared\Domain\Id;
use App\Shared\Infrastructure\Doctrine\Embeddable\Address;
use Symfony\Component\Uid\Uuid;

class OwnerTransformer
{
    public function fromDomain(DomainOwner $owner): OwnerEntity
    {
        return new OwnerEntity(
            id: Uuid::fromString($owner->id->toString()),
            userId: Uuid::fromString($owner->userId->toString()),
            address: Address::fromDomain(new DomainAddress(
                street: $owner->address?->street,
                city: $owner->address?->city,
                postalCode: $owner->address?->postalCode,
            )),
            companyName: $owner->companyName,
            taxId: $owner->taxId,
            phone: $owner->phone,
            firstName: $owner->firstName,
            lastName: $owner->lastName,
            email: $owner->email,
        );
    }

    public function toDomain(OwnerEntity $entity): DomainOwner
    {
        return DomainOwner::restore(
            id: new Id($entity->getId()->toString()),
            userId: new Id($entity->getUserId()->toString()),
            firstName: $entity->getFirstName(),
            lastName: $entity->getLastName(),
            email: $entity->getEmail(),
            companyName: $entity->getCompanyName(),
            taxId: $entity->getTaxId(),
            phone: $entity->getPhone(),
            address: new DomainAddress(
                street: $entity->getAddress()->getStreet(),
                city: $entity->getAddress()->getCity(),
                postalCode: $entity->getAddress()->getPostalCode(),
            )
        );
    }
}
