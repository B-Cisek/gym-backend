<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use App\Subscription\Domain\Embeddable\Interval;
use App\Subscription\Domain\Embeddable\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[Entity]
#[Table(name: 'subscription_plan_prices')]
#[UniqueConstraint(name: 'UNIQ_PLAN_PRICE_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_PLAN_PRICE_STRIPE_PRICE_ID', columns: ['stripe_price_id'])]
final class PlanPrice
{
    use TimestampTrait;

    private function __construct(
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        public Id $id,
        #[Column(name: 'stripe_price_id', type: Types::STRING, unique: true)]
        public string $stripePriceId,
        #[ManyToOne(targetEntity: Plan::class, inversedBy: 'prices')]
        public Plan $plan,
        #[Embedded]
        public Interval $interval,
        #[Embedded]
        public Money $price,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Id $id,
        string $stripePriceId,
        Plan $plan,
        Interval $interval,
        Money $price,
    ): self {
        return new self($id, $stripePriceId, $plan, $interval, $price);
    }

    public function getStripePriceId(): string
    {
        return $this->stripePriceId;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getInterval(): Interval
    {
        return $this->interval;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setInterval(Interval $interval): self
    {
        $this->interval = $interval;

        return $this;
    }
}
