<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Repository;

use App\Gym\Domain\Gym;
use App\Gym\Domain\GymNotFoundException;
use App\Gym\Domain\GymRepository as DomainGymRepository;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;

readonly class GymRepository implements DomainGymRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Gym $gym): void
    {
        $this->entityManager->persist($gym);
        $this->entityManager->flush();
    }

    /**
     * @throws GymNotFoundException
     */
    public function get(Id $id): Gym
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('g')
            ->from(Gym::class, 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new GymNotFoundException()
            : $entity;
    }

    /**
     * @return array<Gym>
     */
    public function findAllByOwnerId(Id $ownerId): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('g')
            ->from(Gym::class, 'g')
            ->where('g.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function delete(Id $id): void
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->delete(Gym::class, 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute()
        ;
    }
}
