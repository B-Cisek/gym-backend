<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Doctrine\Query;

use App\Gym\Application\Query\GetGymsForMenu;
use App\Gym\Application\Query\Result\GymMenu;
use App\Gym\Application\Query\Result\GymMenuCollection;
use App\Gym\Infrastructure\Doctrine\Entity\Gym;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetGymsForMenuQuery implements GetGymsForMenu
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $ownerId): GymMenuCollection
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $result = $queryBuilder
            ->select('g.id', 'g.name')
            ->from(Gym::class, 'g')
            ->where('g.ownerId = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getArrayResult()
        ;

        $gyms = array_map(fn (array $gym) => new GymMenu(
            id: $gym['id']->toString(),
            name: $gym['name'],
        ), $result);

        return new GymMenuCollection($gyms);
    }
}
