<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Repository;

use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepository as DomainUserRepository;
use App\Auth\Infrastructure\Doctrine\Entity\User as UserEntity;
use App\Auth\Infrastructure\Transformer\UserTransformer;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class UserRepository implements DomainUserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserTransformer $transformer
    ) {}

    public function save(User $user, string $hashedPassword): void
    {
        $entity = $this->transformer->fromDomain($user, $hashedPassword);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function get(Id $id): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('u')
            ->from(UserEntity::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id->toString())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new UserNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function getByEmail(Email $email): User
    {
        $qb = $this->entityManager->createQueryBuilder();

        $entity = $qb->select('u')
            ->from(UserEntity::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email->value)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $entity === null
            ? throw new UserNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function existsByEmail(Email $email): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $count = $qb->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email->value)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}
