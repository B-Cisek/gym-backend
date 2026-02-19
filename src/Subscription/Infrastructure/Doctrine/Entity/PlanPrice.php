<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Entity;

use App\Shared\Infrastructure\Doctrine\Embeddable\Interval;
use App\Shared\Infrastructure\Doctrine\Embeddable\Money;
use App\Shared\Infrastructure\Doctrine\Trait\TimestampTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'subscription_plan_prices')]
#[UniqueConstraint(name: 'UNIQ_PLAN_PRICE_ID', columns: ['id'])]
#[UniqueConstraint(name: 'UNIQ_PLAN_PRICE_STRIPE_PRICE_ID', columns: ['stripe_price_id'])]
class PlanPrice
{
    use TimestampTrait;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[Column(type: Types::STRING, unique: true)]
        private string $stripePriceId,
        #[ManyToOne(targetEntity: Plan::class, inversedBy: 'prices')]
        private Plan $plan,
        #[Embedded]
        private Interval $interval,
        #[Embedded]
        private Money $price,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
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

    public function getStripePriceId(): string
    {
        return $this->stripePriceId;
    }

    public function setPrice(Money $price): PlanPrice
    {
        $this->price = $price;
        return $this;
    }

    public function setInterval(Interval $interval): PlanPrice
    {
        $this->interval = $interval;
        return $this;
    }
}
