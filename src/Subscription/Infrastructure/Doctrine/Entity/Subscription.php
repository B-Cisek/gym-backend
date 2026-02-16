<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Entity;

use App\Owner\Domain\Owner;
use App\Shared\Infrastructure\Doctrine\Trait\TimestampTrait;
use App\Subscription\Domain\SubscriptionStatus;
use App\Subscription\Infrastructure\Doctrine\Trait\UniqueStripeIdTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'subscriptions')]
class Subscription
{
    use TimestampTrait;
    use UniqueStripeIdTrait;


    #[Column(Type: Types::INTEGER)]
    private int $paymentAttemptCount = 0;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $nextPaymentAttemptTime = null;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $cancelTime = null;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[ManyToOne(targetEntity: Owner::class)]
        private Owner $owner,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        private PlanPrice $price,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        private PlanPrice $nextPrice,
        #[Column(enumType: SubscriptionStatus::class)]
        private SubscriptionStatus $status,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $startTime,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $endTime,

    )
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
