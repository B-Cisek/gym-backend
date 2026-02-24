<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Http\Controller\V1;

use App\Auth\Application\Query\GetUserInfo;
use App\Auth\Infrastructure\Doctrine\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class GetCurrentUserController
{
    public function __construct(
        private Security $security,
        private GetUserInfo $query
    ) {}

    #[Route('/auth/me', name: 'get_current_user', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $userEntity */
        $userEntity = $this->security->getUser();

        return new JsonResponse($this->query->execute($userEntity->getId()->toString()));
    }
}
