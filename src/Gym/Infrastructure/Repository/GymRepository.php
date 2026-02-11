<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Repository;

use App\Gym\Domain\Gym as DomainGym;
use App\Gym\Domain\GymNotFoundException;
use App\Gym\Domain\GymRepository as DomainGymRepository;
use App\Gym\Infrastructure\Doctrine\Repository\GymRepository as DoctrineGymRepository;
use App\Gym\Infrastructure\Transformer\GymTransformer;
use App\Shared\Domain\Id;
use App\Shared\Infrastructure\Doctrine\Embeddable\Address;

readonly class GymRepository implements DomainGymRepository
{
    public function __construct(
        private DoctrineGymRepository $doctrineRepository,
        private GymTransformer $transformer,
    ) {}

    public function save(DomainGym $gym): void
    {
        $existing = $this->doctrineRepository->get($gym->id->toString());

        if ($existing === null) {
            $entity = $this->transformer->fromDomain($gym);
        } else {
            $existing->setName($gym->name);
            $existing->setAddress(new Address(
                street: $gym->address->street,
                city: $gym->address->city,
                postalCode: $gym->address->postalCode,
            ));
            $existing->setUpdatedAt(new \DateTimeImmutable());
            $entity = $existing;
        }

        $this->doctrineRepository->save($entity);
    }

    public function get(Id $id): DomainGym
    {
        $entity = $this->doctrineRepository->get($id->toString());

        return $entity === null
            ? throw new GymNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    /**
     * @return array<DomainGym>
     */
    public function findAllByOwnerId(Id $ownerId): array
    {
        $entities = $this->doctrineRepository->findAllByOwnerId($ownerId->toString());

        return array_map(
            fn ($entity) => $this->transformer->toDomain($entity),
            $entities,
        );
    }

    public function delete(Id $id): void
    {
        $this->doctrineRepository->delete($id->toString());
    }
}
