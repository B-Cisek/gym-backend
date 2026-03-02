<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Query;

use App\Subscription\Application\Query\GetSubscriptionInfo;
use App\Subscription\Application\Query\Result\SubscriptionInfo;
use App\Subscription\Domain\Subscription;
use Doctrine\ORM\EntityManagerInterface;

readonly class GetSubscriptionInfoQuery implements GetSubscriptionInfo
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $ownerId): SubscriptionInfo
    {
        $qb = $this->entityManager->createQueryBuilder();

        /** @var null|Subscription $subscription */
        $subscription = $qb
            ->select('s')
            ->from(Subscription::class, 's')
            ->leftJoin('s.price', 'pp')
            ->join('s.owner', 'o')
            ->where('o.id = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('s.startTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if ($subscription === null) {
            return new SubscriptionInfo();
        }

        return new SubscriptionInfo(
            id: $subscription->getId()->toString(),
            status: $subscription->getStatus()->value,
            startTime: $subscription->getStartTime()->format(\DateTimeInterface::ATOM),
            endTime: $subscription->getEndTime()->format(\DateTimeInterface::ATOM),
            cancelTime: $subscription->getCancelTime()?->format(\DateTimeInterface::ATOM),
            tier: $subscription->getPrice()->getPlan()->getTier()->value
        );
    }
}
