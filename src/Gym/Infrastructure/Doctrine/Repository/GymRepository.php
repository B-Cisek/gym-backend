<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Doctrine\Repository;

use App\Gym\Infrastructure\Doctrine\Entity\Gym;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GymRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Gym $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function get(string $id): ?Gym
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('g')
            ->from(Gym::class, 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return array<Gym>
     */
    public function findAllByOwnerId(string $ownerId): array
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

    public function delete(string $id): void
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
