<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Query;

use App\Auth\Application\Query\GetUserInfo;
use App\Auth\Application\Query\Result\MeResponse;
use App\Auth\Domain\User;
use App\Owner\Domain\Owner;
use App\Shared\Domain\Id;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

readonly class GetUserInfoQuery implements GetUserInfo
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $userId): MeResponse
    {
        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb->select('u.id', 'u.email', 'u.roles', 'o.isProfileComplete')
            ->from(User::class, 'u')
            ->leftJoin(Owner::class, 'o', Join::WITH, 'o.userId = u.id')
            ->where('u.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getSingleResult()
        ;

        return new MeResponse(
            id: $result['id'] instanceof Id ? $result['id']->toString() : (string) $result['id'],
            email: (string) $result['email'],
            roles: array_map(
                static fn ($role): string => $role instanceof \BackedEnum ? $role->value : (string) $role,
                $result['roles'] ?? []
            ),
            isProfileComplete: $result['isProfileComplete'] ?? false
        );
    }
}
