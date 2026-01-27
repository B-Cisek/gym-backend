<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Transformer;

use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRole;
use App\Auth\Infrastructure\Doctrine\Entity\User as UserEntity;
use App\Shared\Domain\Id;
use Symfony\Component\Uid\Uuid;

class UserTransformer
{
    public function fromDomain(User $user, string $hashedPassword): UserEntity
    {
        return new UserEntity(
            id: Uuid::fromString($user->id->toString()),
            email: $user->email->value,
            password: $hashedPassword,
            roles: array_map(fn (UserRole $role) => $role->value, $user->roles),
        );
    }

    public function toDomain(UserEntity $entity): User
    {
        $roles = array_map(fn (string $role) => UserRole::tryFrom($role), $entity->getRoles());

        return User::restore(
            id: new Id($entity->getId()->toString()),
            email: Email::fromString($entity->getEmail()),
            roles: $roles
        );
    }
}
