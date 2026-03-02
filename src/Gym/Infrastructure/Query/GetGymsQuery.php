<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Query;

use App\Gym\Application\Query\GetGyms;
use App\Gym\Application\Query\Result\Gym as GymResult;
use App\Gym\Application\Query\Result\GymCollection;
use App\Gym\Domain\Gym;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetGymsQuery implements GetGyms
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $ownerId): GymCollection
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $result = $queryBuilder
            ->select(
                'g.id',
                'g.name',
                'g.createdAt',
                'g.address.street',
                'g.address.city',
                'g.address.postalCode',
            )
            ->from(Gym::class, 'g')
            ->where('g.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getArrayResult()
        ;

        $gyms = array_map(fn (array $gym) => new GymResult(
            id: $gym['id']->toString(),
            name: $gym['name'],
            createdAt: $gym['createdAt'],
            street: $gym['address.street'],
            city: $gym['address.city'],
            postalCode: $gym['address.postalCode'],
        ), $result);

        return new GymCollection($gyms);
    }
}
