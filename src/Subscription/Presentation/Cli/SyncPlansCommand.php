<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Cli;

use App\Shared\Application\Service\IdGeneratorInterface;
use App\Subscription\Application\Service\StripeGatewayInterface;
use App\Subscription\Domain\Currency;
use App\Subscription\Domain\Embeddable\Interval;
use App\Subscription\Domain\Embeddable\Money;
use App\Subscription\Domain\IntervalUnit;
use App\Subscription\Domain\Plan;
use App\Subscription\Domain\PlanPrice;
use App\Subscription\Domain\PlanTier;
use App\Subscription\Infrastructure\Repository\PlanRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:stripe:sync-plans')]
readonly class SyncPlansCommand
{
    public function __construct(
        private StripeGatewayInterface $stripeGateway,
        private PlanRepository $planRepository,
        private IdGeneratorInterface $idGenerator
    ) {}

    public function __invoke(OutputInterface $output): int
    {
        $plans = [];

        $prices = $this->stripeGateway->getPrices();

        foreach ($prices['data'] as $data) {
            if (!$data['lookup_key']) {
                continue;
            }

            $tier = PlanTier::from(explode('_', $data['lookup_key'])[0]);
            $gymLimit = $data['metadata']['gyms_limit'] === 'unlimited' ? 0 : (int) $data['metadata']['gyms_limit'];
            $staffLimit = $data['metadata']['staff_limit'] === 'unlimited' ? 0 : (int) $data['metadata']['staff_limit'];

            $existingPlan = $this->planRepository->findByTier($tier);

            if ($existingPlan === null) {
                $plan = new Plan(
                    id: $this->idGenerator->generate(),
                    tier: $tier,
                    gymsLimit: $gymLimit,
                    staffLimit: $staffLimit,
                    isActive: true,
                );

                $plan->addPrice(PlanPrice::create(
                    id: $this->idGenerator->generate(),
                    stripePriceId: $data['id'],
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
                    ->setUpdatedAt(new \DateTimeImmutable())
                ;

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
                    ->setUpdatedAt(new \DateTimeImmutable())
                ;

                $plans[] = $existingPlan;
            }
        }

        $this->planRepository->saveMany($plans);

        return Command::SUCCESS;
    }
}
