<?php

namespace App\Subscription\Infrastructure\Doctrine\Trait;

use Doctrine\ORM\Mapping\Column;

trait UniqueStripeIdTrait
{
    #[Column(unique: true, nullable: true)]
    private ?string $stripeId = null;

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): void
    {
        $this->stripeId = $stripeId;
    }

    public function hasStripeId(): bool
    {
        return null !== $this->stripeId;
    }
}
