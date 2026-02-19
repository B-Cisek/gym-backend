<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\Doctrine\Entity;

use App\Shared\Infrastructure\Doctrine\Trait\TimestampTrait;
use App\Subscription\Domain\PlanTier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'subscription_plans')]
#[UniqueConstraint(name: 'UNIQ_PLAN_ID', columns: ['id'])]
class Plan
{
    use TimestampTrait;

    /** @var Collection<int, PlanPrice> */
    #[OneToMany(targetEntity: PlanPrice::class, mappedBy: 'plan', cascade: ['persist'], orphanRemoval: true)]
    private Collection $prices;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME, length: 36, unique: true)]
        private Uuid $id,
        #[Column(enumType: PlanTier::class)]
        private PlanTier $tier,
        #[Column(type: Types::BOOLEAN)]
        private bool $isActive = true,
        #[Column(type: Types::INTEGER)]
        private int $gymsLimit,
        #[Column(type: Types::INTEGER)]
        private int $staffLimit,
    ) {
        $this->prices = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTier(): PlanTier
    {
        return $this->tier;
    }

    public function getGymsLimit(): int
    {
        return $this->gymsLimit;
    }

    public function getStaffLimit(): int
    {
        return $this->staffLimit;
    }

    /** @return Collection<int, PlanPrice> */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(PlanPrice $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
        }
    }

    public function removePrice(PlanPrice $price): void
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
        }
    }

    public function setTier(PlanTier $tier): Plan
    {
        $this->tier = $tier;

        return $this;
    }

    public function setGymsLimit(int $gymsLimit): Plan
    {
        $this->gymsLimit = $gymsLimit;

        return $this;
    }

    public function setStaffLimit(int $staffLimit): Plan
    {
        $this->staffLimit = $staffLimit;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activePlan(): Plan
    {
        $this->isActive = true;
        return $this;
    }

    public function deactivatePlan(): Plan
    {
        $this->isActive = false;
        return $this;
    }
}
