<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Controller\V1;

use App\Auth\Domain\UserRole;
use App\Owner\Application\Command\Sync\UpdateOwner;
use App\Owner\Application\Query\GetOwnerSettings;
use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Presentation\Http\Request\V1\UpdateOwnerSettingsRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class OwnerSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthContext $authContext,
        private JsonResponseFactory $responseFactory,
    ) {}

    #[Route(path: '/owner/settings', name: 'owner.get', methods: ['GET'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function get(GetOwnerSettings $query): JsonResponse
    {
        $result = $query->execute($this->authContext->getOwnerId());

        return $this->responseFactory->data($result);
    }

    #[Route(path: '/owner/settings', name: 'owner.update', methods: ['PATCH'])]
    #[IsGranted(UserRole::OWNER->value)]
    public function update(#[MapRequestPayload] UpdateOwnerSettingsRequest $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch(new UpdateOwner(
                ownerId: $this->authContext->getOwnerId(),
                firstName: $request->firstName,
                lastName: $request->lastName,
                email: $request->email,
                companyName: $request->companyName,
                taxId: $request->taxId,
                phone: $request->phone,
                street: $request->street,
                city: $request->city,
                postalCode: $request->postalCode,
            ));

            return $this->responseFactory->success();
        } catch (OwnerNotFoundException $e) {
            return $this->responseFactory->error($e->getMessage(), $e->getHttpStatusCode());
        }
    }
}
