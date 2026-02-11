<?php

declare(strict_types=1);

namespace App\Owner\Presentation\Http\Controller\V1;

use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Owner\Application\Command\Sync\UpdateOwner;
use App\Owner\Domain\OwnerNotFoundException;
use App\Owner\Presentation\Http\Request\V1\UpdateOwnerProfileRequest;
use App\Shared\Application\Command\Sync\CommandBus;
use App\Shared\Presentation\Http\Response\JsonResponseFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final readonly class UpdateOwnerProfileController
{
    public function __construct(
        private CommandBus $commandBus,
        private Security $security,
    ) {}

    #[Route('/owner/profile', methods: ['PATCH'])]
    #[IsGranted('ROLE_OWNER')]
    public function __invoke(#[MapRequestPayload] UpdateOwnerProfileRequest $request): JsonResponse
    {
        /** @var User $userEntity */
        $userEntity = $this->security->getUser();

        try {
            $this->commandBus->dispatch(new UpdateOwner(
                userId: $userEntity->getId()->toString(),
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
