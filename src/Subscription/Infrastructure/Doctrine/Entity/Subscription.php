<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Entity;

use App\Owner\Infrastructure\Doctrine\Entity\Owner;
use App\Shared\Infrastructure\Doctrine\Trait\TimestampTrait;
use App\Subscription\Domain\SubscriptionStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'subscriptions')]
#[UniqueConstraint(name: 'UNIQ_SUBSCRIPTION_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_SUBSCRIPTION_STRIPE_SUBSCRIPTION_ID', columns: ['stripe_subscription_id'])]
class Subscription
{
    use TimestampTrait;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $cancelTime = null;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[Column(type: Types::STRING, unique: true)]
        private string $stripeSubscriptionId,
        #[ManyToOne(targetEntity: Owner::class)]
        private Owner $owner,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        private PlanPrice $price,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        private ?PlanPrice $nextPrice,
        #[Column(enumType: SubscriptionStatus::class)]
        private SubscriptionStatus $status,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        private \DateTimeImmutable $startTime,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        private \DateTimeImmutable $endTime,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }
}
