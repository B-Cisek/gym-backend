<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Entity;

use App\Shared\Infrastructure\Doctrine\Trait\TimestampTrait;
use App\Subscription\Infrastructure\Doctrine\Embeddable\Interval;
use App\Subscription\Infrastructure\Doctrine\Embeddable\Money;
use App\Subscription\Infrastructure\Doctrine\Trait\UniqueStripeIdTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'subscription_plan_prices')]
class PlanPrice
{
    use TimestampTrait;
    use UniqueStripeIdTrait;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[ManyToOne(targetEntity: Plan::class, inversedBy: 'prices')]
        private Plan $plan,
        #[Embedded]
        private Interval $interval,
        #[Embedded]
        private Money $price,
    )
    {
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
}
