<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Query;

use App\Subscription\Application\Query\GetCurrentSubscription;
use App\Subscription\Application\Query\Result\CurrentSubscription;
use App\Subscription\Infrastructure\Doctrine\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetCurrentSubscriptionQuery implements GetCurrentSubscription
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $ownerId): ?CurrentSubscription
    {
        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb->select(
            's.id',
            's.status',
            's.startTime',
            's.endTime',
            's.cancelTime',
            'p.tier',
            'pp.price.value AS priceAmount',
            'pp.price.currency AS priceCurrency',
            'pp.interval.value AS intervalValue',
            'pp.interval.unit AS intervalUnit',
        )
            ->from(Subscription::class, 's')
            ->join('s.owner', 'o')
            ->join('s.price', 'pp')
            ->join('pp.plan', 'p')
            ->where('o.id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($result === null) {
            return null;
        }

        return new CurrentSubscription(
            id: (string) $result['id'],
            status: $result['status']->value,
            planTier: $result['tier']->value,
            priceAmount: $result['priceAmount'],
            priceCurrency: $result['priceCurrency']->value,
            intervalUnit: $result['intervalUnit']->value,
            intervalValue: $result['intervalValue'],
            startTime: $result['startTime']->format('c'),
            endTime: $result['endTime']?->format('c'),
            cancelTime: $result['cancelTime']?->format('c'),
        );
    }
}
