<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Controller\V1;

use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Owner\Application\Command\Sync\UpdateOwner;
use App\Owner\Application\Query\GetOwnerSettings;
use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Presentation\Http\Request\V1\UpdateOwnerSettingsRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Infrastructure\Security\AuthContext;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class OwnerSettingsController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly AuthContext $authContext,
    ) {}

    #[Route(path: '/owner/settings', name: 'owner.get', methods: ['GET'])]
    public function get(GetOwnerSettings $query): JsonResponse
    {
        $result = $query->execute($this->authContext->getOwnerId());

        return $this->json($result);
    }

    #[Route(path: '/owner/settings', name: 'owner.update', methods: ['PATCH'])]
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

            return JsonResponseFactory::success();
        } catch (OwnerNotFoundException $e) {
            return JsonResponseFactory::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
