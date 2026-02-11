<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Repository;

use App\Owner\Domain\Owner as DomainOwner;
use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Domain\OwnerRepository as DomainOwnerRepository;
use App\Owner\Infrastructure\Doctrine\Repository\OwnerRepository as DoctrineOwnerRepository;
use App\Owner\Infrastructure\Transformer\OwnerTransformer;
use App\Shared\Domain\Id;
use App\Shared\Infrastructure\Doctrine\Embeddable\Address;

readonly class OwnerRepository implements DomainOwnerRepository
{
    public function __construct(
        private DoctrineOwnerRepository $doctrineRepository,
        private OwnerTransformer $transformer,
    ) {}

    public function save(DomainOwner $owner): void
    {
        $existing = $this->doctrineRepository->get($owner->id->toString());

        if ($existing === null) {
            $entity = $this->transformer->fromDomain($owner);
        } else {
            $existing->setCompanyName($owner->companyName);
            $existing->setTaxId($owner->taxId);
            $existing->setPhone($owner->phone);
            $existing->setAddress(new Address(
                street: $owner->address->street,
                city: $owner->address->city,
                postalCode: $owner->address->postalCode,
                voivodeship: $owner->address->voivodeship,
            ));
            $entity = $existing;
        }

        $this->doctrineRepository->save($entity);
    }

    public function get(Id $id): DomainOwner
    {
        $entity = $this->doctrineRepository->get($id->toString());

        return $entity === null
            ? throw new OwnerNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function getByUserId(Id $userId): DomainOwner
    {
        $entity = $this->doctrineRepository->getByUserId($userId->toString());

        return $entity === null
            ? throw new OwnerNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function existsByUserId(Id $userId): bool
    {
        return $this->doctrineRepository->existsByUserId($userId->toString());
    }
}
