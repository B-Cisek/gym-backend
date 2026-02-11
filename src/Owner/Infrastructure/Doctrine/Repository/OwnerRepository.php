<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Doctrine\Repository;

use App\Owner\Infrastructure\Doctrine\Entity\Owner;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OwnerRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Owner $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function get(string $id): ?Owner
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('o')
            ->from(Owner::class, 'o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getByUserId(string $userId): ?Owner
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('o')
            ->from(Owner::class, 'o')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function existsByUserId(string $userId): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $count = $qb->select('COUNT(o.id)')
            ->from(Owner::class, 'o')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    public function getIdByUserId(string $userId): string
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('o.id')
            ->from(Owner::class, 'o')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
