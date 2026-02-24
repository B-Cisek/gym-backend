<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Command\Sync\CreateCheckoutSession;
use App\Subscription\Presentation\Http\Request\V1\CreateCheckoutSessionRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class CheckoutController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthContext $authContext,
        private JsonResponseFactory $jsonResponseFactory,
    ) {}

    #[Route(path: '/subscriptions/checkout', name: 'subscriptions.checkout', methods: ['POST'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function __invoke(#[MapRequestPayload] CreateCheckoutSessionRequest $request): JsonResponse
    {
        /** @var string $checkoutUrl */
        $checkoutUrl = $this->commandBus->dispatch(new CreateCheckoutSession(
            ownerId: $this->authContext->getOwnerId(),
            planPriceId: $request->planPriceId,
        ));

        return $this->jsonResponseFactory->data(['url' => $checkoutUrl]);
    }
}
