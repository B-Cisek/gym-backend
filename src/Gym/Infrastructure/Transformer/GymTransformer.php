<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Transformer;

use App\Gym\Domain\Gym as DomainGym;
use App\Gym\Infrastructure\Doctrine\Entity\Gym as GymEntity;
use App\Shared\Domain\Id;
use App\Shared\Infrastructure\Doctrine\Embeddable\Address as AddressEmbeddable;
use Symfony\Component\Uid\Uuid;

class GymTransformer
{
    public function fromDomain(DomainGym $gym): GymEntity
    {
        return new GymEntity(
            id: Uuid::fromString($gym->id->toString()),
            ownerId: Uuid::fromString($gym->ownerId->toString()),
            name: $gym->name,
            address: AddressEmbeddable::fromDomain($gym->address),
        );
    }

    public function toDomain(GymEntity $entity): DomainGym
    {
        return DomainGym::restore(
            id: new Id($entity->getId()->toString()),
            ownerId: new Id($entity->getOwnerId()->toString()),
            name: $entity->getName(),
            address: $entity->getAddress()->toDomain(),
        );
    }
}
