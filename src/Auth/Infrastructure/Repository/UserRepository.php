<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserNotFoundException;
use App\Auth\Domain\UserRepository as DomainUserRepository;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserRepository implements DomainUserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function get(Id $id): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new UserNotFoundException()
            : $entity;
    }

    public function getByEmail(Email $email): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new UserNotFoundException()
            : $entity;
    }

    public function existsByEmail(Email $email): bool
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
