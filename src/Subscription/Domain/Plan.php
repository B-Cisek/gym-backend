<?php

declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\Id;
use App\Shared\Domain\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id as DoctrineId;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

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
        #[DoctrineId]
        #[Column(type: 'id', unique: true)]
        private Id $id,
        #[Column(enumType: PlanTier::class)]
        private PlanTier $tier,
        #[Column(type: Types::INTEGER)]
        private int $gymsLimit,
        #[Column(type: Types::INTEGER)]
        private int $staffLimit,
        #[Column(type: Types::BOOLEAN)]
        private bool $isActive = true,
    ) {
        $this->prices = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Id
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

    public function setTier(PlanTier $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function setGymsLimit(int $gymsLimit): self
    {
        $this->gymsLimit = $gymsLimit;

        return $this;
    }

    public function setStaffLimit(int $staffLimit): self
    {
        $this->staffLimit = $staffLimit;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activePlan(): self
    {
        $this->isActive = true;

        return $this;
    }

    public function deactivatePlan(): self
    {
        $this->isActive = false;

        return $this;
    }
}
