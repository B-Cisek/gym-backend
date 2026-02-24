<?php

declare(strict_types=1);

namespace App\Subscription\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use App\Subscription\Application\Command\Sync\CreatePortalSession;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class PortalController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthContext $authContext,
        private JsonResponseFactory $jsonResponseFactory,
    ) {}

    #[Route(path: '/subscriptions/portal', name: 'subscriptions.portal', methods: ['POST'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function __invoke(): JsonResponse
    {
        /** @var string $portalUrl */
        $portalUrl = $this->commandBus->dispatch(new CreatePortalSession(
            ownerId: $this->authContext->getOwnerId(),
        ));

        return $this->jsonResponseFactory->data(['url' => $portalUrl]);
    }
}
