<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Repository;

use App\Auth\Infrastructure\Doctrine\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(User $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function get(string $id): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getByEmail(string $email): ?User
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function existsByEmail(string $email): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $count = $qb->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}
