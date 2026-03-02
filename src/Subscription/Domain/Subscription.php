<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Owner\Domain\Owner;
use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[Entity]
#[Table(name: 'subscriptions')]
#[UniqueConstraint(name: 'UNIQ_SUBSCRIPTION_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_SUBSCRIPTION_STRIPE_SUBSCRIPTION_ID', columns: ['stripe_subscription_id'])]
final class Subscription
{
    use TimestampTrait;

    private function __construct(
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        public Id $id,
        #[Column(type: Types::STRING, unique: true)]
        public string $stripeSubscriptionId,
        #[ManyToOne(targetEntity: Owner::class)]
        public Owner $owner,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        public PlanPrice $price,
        #[ManyToOne(targetEntity: PlanPrice::class)]
        public ?PlanPrice $nextPrice,
        #[Column(enumType: SubscriptionStatus::class)]
        public SubscriptionStatus $status,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $startTime,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $endTime,
        #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        public ?\DateTimeImmutable $cancelTime,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Id $id,
        Owner $owner,
        PlanPrice $planPrice,
        SubscriptionStatus $status,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        string $stripeSubscriptionId,
        ?\DateTimeImmutable $cancelTime = null,
    ): self {
        return new self($id, $stripeSubscriptionId, $owner, $planPrice, null, $status, $startTime, $endTime, $cancelTime);
    }

    public static function restore(
        Id $id,
        Owner $owner,
        PlanPrice $planPrice,
        ?PlanPrice $nextPlanPrice,
        SubscriptionStatus $status,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $endTime,
        string $stripeSubscriptionId,
        ?\DateTimeImmutable $cancelTime,
    ): self {
        return new self($id, $stripeSubscriptionId, $owner, $planPrice, $nextPlanPrice, $status, $startTime, $endTime, $cancelTime);
    }

    public function update(
        SubscriptionStatus $status,
        \DateTimeImmutable $endTime,
        ?\DateTimeImmutable $cancelTime,
    ): self {
        $this->status = $status;
        $this->endTime = $endTime;
        $this->cancelTime = $cancelTime;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getCancelTime(): ?\DateTimeImmutable
    {
        return $this->cancelTime;
    }

    public function getStripeSubscriptionId(): string
    {
        return $this->stripeSubscriptionId;
    }

    public function getPrice(): PlanPrice
    {
        return $this->price;
    }

    public function getNextPrice(): ?PlanPrice
    {
        return $this->nextPrice;
    }
}
