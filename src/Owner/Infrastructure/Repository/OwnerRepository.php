<?php

declare(strict_types=1);

namespace App\Owner\Infrastructure\Repository;

use App\Owner\Domain\Owner;
use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Domain\OwnerRepository as DomainOwnerRepository;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;

readonly class OwnerRepository implements DomainOwnerRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Owner $owner): void
    {
        $this->entityManager->persist($owner);
        $this->entityManager->flush();
    }

    public function get(Id $id): Owner
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('o')
            ->from(Owner::class, 'o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new OwnerNotFoundException()
            : $entity;
    }

    public function getByUserId(Id $userId): Owner
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('o')
            ->from(Owner::class, 'o')
            ->where('o.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new OwnerNotFoundException()
            : $entity;
    }

    public function existsByUserId(Id $userId): bool
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
}
