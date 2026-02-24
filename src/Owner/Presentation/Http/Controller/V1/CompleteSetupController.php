<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Owner\Application\Command\Sync\CompleteSetup;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class CompleteSetupController
{
    public function __construct(
        private JsonResponseFactory $responseFactory,
        private CommandBus $commandBus,
        private AuthContext $authContext,
    ) {}

    #[Route('/complete-setup', name: 'complete.setup', methods: ['POST'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function completeSetup(): JsonResponse
    {
        $this->commandBus->dispatch(new CompleteSetup($this->authContext->getOwnerId()));

        return $this->responseFactory->noContent();
    }
}
