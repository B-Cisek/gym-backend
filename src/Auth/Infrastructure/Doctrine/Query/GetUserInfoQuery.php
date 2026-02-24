<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Query;

use App\Auth\Application\Query\GetUserInfo;
use App\Auth\Application\Query\Result\MeResponse;
use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Owner\Infrastructure\Doctrine\Entity\Owner;
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
            ->getsingleResult()
        ;

        return new MeResponse(
            id : $result['id']->toString(),
            email : $result['email'],
            roles: $result['roles'],
            isProfileComplete: $result['isProfileComplete'] ?? false
        );
    }
}
