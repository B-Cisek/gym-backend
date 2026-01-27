<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Repository;

use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserNotFoundException;
use App\Auth\Domain\UserRepository as DomainUserRepository;
use App\Auth\Infrastructure\Transformer\UserTransformer;
use App\Shared\Domain\Id;
use App\Auth\Infrastructure\Doctrine\Repository\UserRepository as DoctrineUserRepository;

readonly class UserRepository implements DomainUserRepository
{
    public function __construct(
        private DoctrineUserRepository $doctrineRepository,
        private UserTransformer $transformer
    )
    {
    }

    public function save(User $user, string $hashedPassword): void
    {
        $entity = $this->transformer->fromDomain($user, $hashedPassword);

        $this->doctrineRepository->save($entity);
    }

    public function get(Id $id): User
    {
        $entity = $this->doctrineRepository->get($id->toString());

        return $entity === null
            ? throw new UserNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function getByEmail(Email $email): User
    {
        $entity = $this->doctrineRepository->getByEmail($email->value);

        return $entity === null
            ? throw new UserNotFoundException()
            : $this->transformer->toDomain($entity);
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->doctrineRepository->existsByEmail($email->value);
    }
}
