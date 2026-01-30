<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Infrastructure\Doctrine\Entity\User;
use App\Auth\Presentation\Http\Response\V1\MeResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class GetCurrentUserController
{
    public function __construct(
        private Security $security,
    ) {}

    #[Route('/auth/me', name: 'get_current_user', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $userEntity */
        $userEntity = $this->security->getUser();

        return new JsonResponse(new MeResponse(
            id: $userEntity->getId()->toString(),
            email: $userEntity->getEmail(),
            roles: $userEntity->getRoles(),
        ));
    }
}
