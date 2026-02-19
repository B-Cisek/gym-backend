<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Cli;

use App\Shared\Application\Service\IdGeneratorInterface;
use App\Shared\Infrastructure\Doctrine\Embeddable\Interval;
use App\Shared\Infrastructure\Doctrine\Embeddable\Money;
use App\Subscription\Application\Service\StripeGatewayInterface;
use App\Subscription\Domain\Currency;
use App\Subscription\Domain\IntervalUnit;
use App\Subscription\Domain\PlanTier;
use App\Subscription\Infrastructure\Doctrine\Entity\Plan;
use App\Subscription\Infrastructure\Doctrine\Entity\PlanPrice;
use App\Subscription\Infrastructure\Doctrine\Repository\PlanRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:stripe:sync-plans')]
readonly class SyncPlansCommand
{
    public function __construct(
        private StripeGatewayInterface $stripeGateway,
        private PlanRepository $planRepository,
        private IdGeneratorInterface $idGenerator
    ) {
    }

    public function __invoke(OutputInterface $output): int
    {
        $plans = [];

        $collection = $this->stripeGateway->getPrices();

        foreach ($collection->data as $price) {
            $data = $price->toArray();

            $tier = PlanTier::from(explode('_', $data['lookup_key'])[0]);
            $gymLimit = $data['metadata']['gyms_limit'] === 'unlimited' ? 0 : (int) $data['metadata']['gyms_limit'];
            $staffLimit = $data['metadata']['staff_limit'] === 'unlimited' ? 0 : (int) $data['metadata']['staff_limit'];

            $existingPlan = $this->planRepository->findByTier($tier);

            if ($existingPlan === null) {
                $plan = new Plan(
                    id: Uuid::fromString($this->idGenerator->generate()->toString()),
                    tier: $tier,
                    isActive: true,
                    gymsLimit: $gymLimit,
                    staffLimit: $staffLimit,
                );

                $plan->addPrice(new PlanPrice(
                    id: Uuid::fromString($this->idGenerator->generate()->toString()),
                    stripePriceId: $price->id,
                    plan: $plan,
                    interval: new Interval(
                        value: $data['recurring']['interval_count'],
                        unit: IntervalUnit::from($data['recurring']['interval'])
                    ),
                    price: new Money(
                        value: $data['unit_amount'],
                        currency: Currency::from(mb_strtoupper($data['currency']))
                    )
                ));

                $plans[] = $plan;
            } else {
                $existingPlan
                    ->setGymsLimit($gymLimit)
                    ->setStaffLimit($staffLimit)
                    ->setUpdatedAt(new \DateTimeImmutable());

                // the assumption that there will be only one price
                // dont work for many tier prices
                $price = $existingPlan->getPrices()->first();

                if (!$price) {
                    throw new \RuntimeException('Plan has no price');
                }

                $price
                    ->setPrice(new Money(
                        value: $data['unit_amount'],
                        currency: Currency::from(mb_strtoupper($data['currency']))
                    ))
                    ->setInterval(new Interval(
                        value: $data['recurring']['interval_count'],
                        unit: IntervalUnit::from($data['recurring']['interval'])
                    ))
                    ->setUpdatedAt(new \DateTimeImmutable());

                $plans[] = $existingPlan;
            }
        }

        $this->planRepository->saveMany($plans);

        return Command::SUCCESS;
    }
}
